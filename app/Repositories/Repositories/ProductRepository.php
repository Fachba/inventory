<?php

namespace App\Repositories\Repositories;

use App\Models\Product;
use App\Repositories\Interface\ProductInterface;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductInterface
{
    public function getFinalStockQueryJoin(string $productColumn): string
    {
        return "/* ===================================================== */
                /* 1️⃣ Last Approved Opname Date Per Product */
                /* ===================================================== */
                LEFT JOIN (
                    SELECT
                        sod.product_id,
                        MAX(sod.created_at) AS last_opname_date
                    FROM stock_opname_detail sod
                    JOIN stock_opnames so
                        ON sod.stock_opname_id = so.stock_opname_id
                        AND so.deleted_at IS NULL
                        AND so.status_id = 4
                    WHERE sod.deleted_at IS NULL
                    GROUP BY sod.product_id
                ) last_opname
                    ON last_opname.product_id = {$productColumn}


                /* ===================================================== */
                /* 2️⃣ Latest Physical Stock */
                /* ===================================================== */
                LEFT JOIN (
                    SELECT t.product_id, t.physical_stock
                    FROM stock_opname_detail t
                    WHERE t.deleted_at IS NULL
                    AND t.created_at = (
                        SELECT MAX(s2.created_at)
                        FROM stock_opname_detail s2
                        WHERE s2.product_id = t.product_id
                        AND s2.deleted_at IS NULL
                    )
                ) latest
                    ON latest.product_id = {$productColumn}


                /* ===================================================== */
                /* 3️⃣ Sum Opname After Last */
                /* ===================================================== */
                LEFT JOIN (
                    SELECT
                        sod.product_id,
                        SUM(sod.physical_stock) AS qty
                    FROM stock_opname_detail sod
                    JOIN stock_opnames so
                        ON sod.stock_opname_id = so.stock_opname_id
                        AND so.deleted_at IS NULL
                        AND so.status_id = 4
                    WHERE sod.deleted_at IS NULL
                    GROUP BY sod.product_id
                ) opname_sum
                    ON opname_sum.product_id = {$productColumn}


                /* ===================================================== */
                /* 4️⃣ Good Receive After Last */
                /* ===================================================== */
                LEFT JOIN (
                    SELECT
                        grd.product_id,
                        SUM(grd.qty_gr) AS qty
                    FROM good_receive_detail grd
                    WHERE grd.deleted_at IS NULL
                    GROUP BY grd.product_id
                ) gr_sum
                    ON gr_sum.product_id = {$productColumn}


                /* ===================================================== */
                /* 5️⃣ Request Product */
                /* ===================================================== */
                LEFT JOIN (
                    SELECT
                        rpd.product_id,
                        SUM(rpd.qty_rp) AS qty
                    FROM request_product_detail rpd
                    WHERE rpd.deleted_at IS NULL
                    GROUP BY rpd.product_id
                ) rp_sum
                    ON rp_sum.product_id = {$productColumn}";
    }

    public function getAll($limit, $offset)
    {
        // Ambil subquery stock final untuk setiap produk
        $leftJoinStock = $this->getFinalStockQueryJoin('a.product_id');

        $query = "
            SELECT a.*,
                COUNT(*) OVER() AS total,
                COALESCE(latest.physical_stock,
                    COALESCE(opname_sum.qty,0)
                    + COALESCE(gr_sum.qty,0)
                    - COALESCE(rp_sum.qty,0)
                ) AS product_stock_left,
                u.user_name as updated_by_name,
            FROM products a
            LEFT JOIN users u ON u.user_id = a.user_upd
            $leftJoinStock
            WHERE a.deleted_at IS NULL

            ORDER BY a.product_id
            LIMIT ? OFFSET ?
        ";

        // var_dump($query);

        return DB::select($query, [$limit, $offset]);
    }

    public function find(int $id)
    {
        // Ambil subquery stock final untuk setiap produk
        $leftJoinStock = $this->getFinalStockQueryJoin('a.product_id');

        $query = "
            SELECT a.*,
                COUNT(*) OVER() AS total,
                COALESCE(latest.physical_stock,
                    COALESCE(opname_sum.qty,0)
                    + COALESCE(gr_sum.qty,0)
                    - COALESCE(rp_sum.qty,0)
                ) AS product_stock_left,
                u.user_name as updated_by_name
            FROM products a
            LEFT JOIN users u ON u.user_id = a.user_upd
            $leftJoinStock
            WHERE a.deleted_at IS NULL AND a.product_id = ?
            ORDER BY a.product_id
        ";

        return DB::select($query, [$id]);
    }
}
