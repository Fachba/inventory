<?php

namespace App\Repositories\Repositories;

use App\Models\Product;
use App\Repositories\Interface\ProductInterface;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductInterface
{
    public function getFinalStockQueryJoin(string $productColumn): string
    {
        return "

        /* ===================================================== */
        /* 1️⃣ Last Opname Date Per Product */
        /* ===================================================== */
        LEFT JOIN (
            SELECT
                sod.product_id,
                MAX(sod.created_at) AS last_opname_date
            FROM stock_opname_detail sod
            WHERE sod.deleted_at IS NULL
            GROUP BY sod.product_id
        ) last_opname
            ON last_opname.product_id = {$productColumn}


        /* ===================================================== */
        /* 2️⃣ Latest Physical Stock (based on last opname date) */
        /* ===================================================== */
        LEFT JOIN (
            SELECT
                sod.product_id,
                sod.physical_stock
            FROM stock_opname_detail sod
            WHERE sod.deleted_at IS NULL
            AND sod.created_at = (
                SELECT MAX(s2.created_at)
                FROM stock_opname_detail s2
                WHERE s2.product_id = sod.product_id
                AND s2.deleted_at IS NULL
            )
        ) latest
            ON latest.product_id = {$productColumn}


        /* ===================================================== */
        /* 3️⃣ Good Receive AFTER last opname */
        /* ===================================================== */
        LEFT JOIN (
            SELECT
                grd.product_id,
                SUM(grd.qty_gr) AS qty
            FROM good_receive_detail grd
            LEFT JOIN (
                SELECT
                    sod.product_id,
                    MAX(sod.created_at) AS last_opname_date
                FROM stock_opname_detail sod
                WHERE sod.deleted_at IS NULL
                GROUP BY sod.product_id
            ) lo ON lo.product_id = grd.product_id
            WHERE grd.deleted_at IS NULL
            AND (
                    lo.last_opname_date IS NULL
                    OR grd.created_at > lo.last_opname_date
                )
            GROUP BY grd.product_id
        ) gr_sum
            ON gr_sum.product_id = {$productColumn}


        /* ===================================================== */
        /* 4️⃣ Request Product AFTER last opname */
        /* ===================================================== */
        LEFT JOIN (
            SELECT
                rpd.product_id,
                SUM(rpd.qty_rp) AS qty
            FROM request_product_detail rpd
            LEFT JOIN (
                SELECT
                    sod.product_id,
                    MAX(sod.created_at) AS last_opname_date
                FROM stock_opname_detail sod
                WHERE sod.deleted_at IS NULL
                GROUP BY sod.product_id
            ) lo ON lo.product_id = rpd.product_id
            WHERE rpd.deleted_at IS NULL
            AND (
                    lo.last_opname_date IS NULL
                    OR rpd.created_at > lo.last_opname_date
                )
            GROUP BY rpd.product_id
        ) rp_sum
            ON rp_sum.product_id = {$productColumn}
    ";
    }

    public function getAll($limit, $offset)
    {
        $leftJoinStock = $this->getFinalStockQueryJoin('a.product_id');

        $query = "
        SELECT 
            a.*,
            COUNT(*) OVER() AS total,
            (
                COALESCE(latest.physical_stock, 0)
                + COALESCE(gr_sum.qty, 0)
                - COALESCE(rp_sum.qty, 0)
            ) AS product_stock_left,
            u.user_name as updated_by_name
        FROM products a
        LEFT JOIN users u 
            ON u.user_id = a.user_upd
        $leftJoinStock
        WHERE a.deleted_at IS NULL
        ORDER BY a.product_id
        LIMIT ? OFFSET ?
    ";

        return DB::select($query, [$limit, $offset]);
    }

    public function find(int $id)
    {
        // Ambil subquery stock final untuk setiap produk
        $leftJoinStock = $this->getFinalStockQueryJoin('a.product_id');

        $query = "
            SELECT a.*,
                COUNT(*) OVER() AS total,
                (
                    COALESCE(latest.physical_stock, 0)
                    + COALESCE(gr_sum.qty, 0)
                    - COALESCE(rp_sum.qty, 0)
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

    public function movementStockProduct(int $id, int $limit, int $offset)
    {
        $query = "
            SELECT 
                a.*,
                mp.product_name,
                COUNT(*) OVER() AS total,
                SUM(qty_in - qty_out) OVER (
                    ORDER BY a.tanggal 
                    ROWS UNBOUNDED PRECEDING
                ) AS running_stock
            FROM (
                
                /* ================= REQUEST PRODUCT (OUT) ================= */
                SELECT 
                    rp.created_at AS tanggal,
                    rp.product_id,
                    rp.qty_rp AS qty_out,
                    0 AS qty_in,
                    'Request Product' AS transaksi,
                    u.user_name AS person_name
                FROM request_product_detail rp
                LEFT JOIN users u 
                    ON u.user_id = rp.user_add
                WHERE rp.deleted_at IS NULL 
                AND rp.product_id = ?

                UNION ALL

                /* ================= Good Receive (IN) ================= */
                SELECT 
                    gr.created_at AS tanggal,
                    gr.product_id,
                    0 AS qty_out,
                    gr.qty_gr AS qty_in,
                    'Good Receive' AS transaksi,
                    u.user_name AS person_name
                FROM good_receive_detail gr
                LEFT JOIN users u 
                    ON u.user_id = gr.user_add
                WHERE gr.deleted_at IS NULL 
                AND gr.product_id = ?

                UNION ALL

                /* ================= CORRECTION ================= */
                SELECT 
                    so.created_at AS tanggal,
                    so.product_id,
                    CASE WHEN COALESCE(so.physical_stock,0)-COALESCE(so.system_stock,0) < 0 THEN ABS(so.physical_stock-so.system_stock) ELSE 0 END AS qty_out,
                    CASE WHEN COALESCE(so.physical_stock,0)-COALESCE(so.system_stock,0) > 0 THEN COALESCE(so.physical_stock,0)-COALESCE(so.system_stock,0) ELSE 0 END AS qty_in,
                    'Stock Opname' AS transaksi,
                    u.user_name AS person_name
                FROM stock_opname_detail so
                LEFT JOIN users u 
                    ON u.user_id = so.user_add
                WHERE so.deleted_at IS NULL 
                AND so.product_id = ?

            ) a

            LEFT JOIN products mp 
                ON mp.product_id = a.product_id

            ORDER BY a.tanggal ASC
            LIMIT ? OFFSET ?
        ";

        return DB::select($query, [$id, $id, $id, $limit, $offset]);
    }
}
