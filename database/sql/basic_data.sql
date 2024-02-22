INSERT INTO `category` (`id`, `nama`, `slug`, `status`, `key`, `created_at`, `updated_at`)
VALUES
(1,'Carcass','carcass',NULL,NULL,NULL,NULL),
(2,'Parting','parting',NULL,NULL,NULL,NULL),
(3,'Marinated','marinated',NULL,NULL,NULL,NULL),
(4,'By Product','by-product',NULL,NULL,NULL,NULL),
(5,'Boneless','boneless',NULL,NULL,NULL,NULL),
(6,'By Product Boneless','by-product-boneless',NULL,NULL,NULL,NULL),
(7,'Carcass Frozen','carcass-frozen',NULL,NULL,NULL,NULL),
(8,'Parting Frozen','parting-frozen',NULL,NULL,NULL,NULL),
(9,'Marinated Frozen','marinated-frozen',NULL,NULL,NULL,NULL),
(10,'By Product Frozen','by-product-frozen',NULL,NULL,NULL,NULL),
(11,'Boneless Frozen','boneless-frozen',NULL,NULL,NULL,NULL),
(12,'Pejantan','pejantan',NULL,NULL,NULL,NULL),
(13,'Pejantan Frozen','pejantan-frozen',NULL,NULL,NULL,NULL),
(14,'Parent','Parent',NULL,NULL,NULL,NULL),
(15,'Parent Frozen','parent-frozen',NULL,NULL,NULL,NULL),
(16,'By Product Boneless Frozen','by-product-boneless-frozen',NULL,NULL,NULL,NULL),
(17,'Whole Chicken','whole-chicken',NULL,NULL,NULL,NULL),
(18,'Whole Chicken Frozen','whole-chicken-frozen',NULL,NULL,NULL,NULL),
(19,'Ayam Kampung','ayam-kampung',NULL,NULL,NULL,NULL),
(20,'Ayam Kampung Frozen','ayam-kampung-frozen',NULL,NULL,NULL,NULL),
(21,'Plastik','plastik',NULL,NULL,NULL,NULL),
(22,'Live Bird','live-bird',NULL,NULL,NULL,NULL),
(23,'Others','others',NULL,NULL,'2021-08-23 21:34:47','2021-08-23 21:34:47'),
(24,'Chemical','chemical',NULL,NULL,'2021-08-23 21:34:47','2021-08-23 21:34:47'),
(25,'Packaging','packaging',NULL,NULL,'2021-08-23 21:34:47','2021-08-23 21:34:47'),
(26,'Medicine','medicine',NULL,NULL,'2021-08-23 21:34:48','2021-08-23 21:34:48'),
(27,'Atk & Perlengkapan Produksi','atk-&-perlengkapan-produksi',NULL,NULL,'2021-08-23 21:34:48','2021-08-23 21:34:48'),
(28,'Sparepart','sparepart',NULL,NULL,'2021-08-23 21:34:52','2021-08-23 21:34:52');

INSERT INTO `marketing` (`id`, `nama`, `user_id`, `alamat`, `telp`, `kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `key`, `created_at`, `updated_at`) VALUES
(1, 'SUBANDI EBA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-31 04:48:49', '2021-05-31 04:48:49'),
(2, 'MILAGRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-31 04:48:49', '2021-05-31 04:48:49'),
(3, 'SONY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-31 04:48:49', '2021-05-31 04:48:49'),
(4, 'SETYO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-31 04:48:49', '2021-05-31 04:48:49'),
(5, 'ANDI EBA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-31 04:48:49', '2021-05-31 04:48:49'),
(6, 'SAKTI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-31 04:48:52', '2021-05-31 04:48:52'),
(7, 'IFAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-31 04:48:52', '2021-05-31 04:48:52'),
(8, 'SANDY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-31 04:48:53', '2021-05-31 04:48:53');

INSERT INTO `company` (`id`, `code`, `nama`, `alamat`, `telp`, `kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `created_at`, `updated_at`) VALUES (1, 'CGL', 'Citra Guna Lestari', 'Jl. Telaga Mas Raya No.29, Talaga, Kec. Cikupa, Tangerang, Banten 15710', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `company` (`id`, `code`, `nama`, `alamat`, `telp`, `kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `created_at`, `updated_at`) VALUES (2, 'EBA', 'Efran Berkat Aditama', 'Jl. KH. Wachid Hasyim, Sawo, Kec. Jetis, Mojokerto, Jawa Timur 61353', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `gudang` (`id`, `kategori`, `subsidiary`, `subsidiary_id`, `code`, `company_id`, `netsuite_internal_id`, `status`, `created_at`, `updated_at`, `key`, `deleted_at`) VALUES
(1, 'Production', 'CGL', 6, 'CGL - Storage Live Bird', 1, 49, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(2, 'Production', 'CGL', 6, 'CGL - Chiller Bahan Baku', 1, 36, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(3, 'Production', 'CGL', 6, 'CGL - Storage Produksi (WIP)', 1, 50, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(4, 'Production', 'CGL', 6, 'CGL - Chiller Finished Good', 1, 37, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(5, 'Production', 'CGL', 6, 'CGL - Storage Expedisi', 1, 48, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(6, 'Warehouse', 'CGL', 6, 'CGL - Storage ABF', 1, 46, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(7, 'Warehouse', 'CGL', 6, 'CGL - Cold Storage 1', 1, 38, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(8, 'Warehouse', 'CGL', 6, 'CGL - Cold Storage 2', 1, 39, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(9, 'Warehouse', 'CGL', 6, 'CGL - Cold Storage 3', 1, 40, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(10, 'Warehouse', 'CGL', 6, 'CGL - Cold Storage 4', 1, 41, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(11, 'Warehouse', 'CGL', 6, 'CGL - Storage DHK', 1, 47, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(12, 'Production', 'CGL', 6, 'CGL - Storage Retur', 1, 51, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(13, 'Production', 'CGL', 6, 'CGL - Storage Susut', 1, 52, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(14, 'Others', 'CGL', 6, 'CGL - Others', 1, 43, NULL, NULL, '2021-08-23 07:42:12', NULL, NULL),
(15, 'Others', 'CGL', 6, 'CGL NONE', NULL, 32, NULL, '2021-08-02 09:37:44', '2021-08-23 07:42:12', NULL, NULL),
(16, 'Others', 'CGL', 6, 'CGL - ATK & Perlengkapan', NULL, 34, NULL, '2021-08-02 09:37:44', '2021-08-23 07:42:12', NULL, NULL),
(17, 'Others', 'CGL', 6, 'CGL - Sparepart', NULL, 45, NULL, '2021-08-02 09:37:44', '2021-08-23 07:42:12', NULL, NULL),
(18, 'Others', 'CGL', 6, 'CGL - Chemical', NULL, 35, NULL, '2021-08-02 09:37:44', '2021-08-23 07:42:12', NULL, NULL),
(19, 'Others', 'CGL', 6, 'CGL - Packaging', NULL, 44, NULL, '2021-08-02 09:37:44', '2021-08-23 07:42:12', NULL, NULL),
(20, 'Others', 'CGL', 6, 'CGL - Medicine', NULL, 42, NULL, '2021-08-02 09:37:44', '2021-08-23 07:42:12', NULL, NULL),
(21, NULL, NULL, NULL, 'EBA - ATK & Perlengkapan', NULL, 53, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(22, NULL, NULL, NULL, 'EBA - Chemical', NULL, 54, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(23, NULL, NULL, NULL, 'EBA - Chiller Bahan Baku', NULL, 55, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(24, NULL, NULL, NULL, 'EBA - Chiller Finished Good', NULL, 56, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(25, NULL, NULL, NULL, 'EBA - Cold Storage 1', NULL, 57, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(26, NULL, NULL, NULL, 'EBA - Cold Storage 2', NULL, 58, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(27, NULL, NULL, NULL, 'EBA - Cold Storage 3', NULL, 59, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(28, NULL, NULL, NULL, 'EBA - Cold Storage 4', NULL, 60, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(29, NULL, NULL, NULL, 'EBA - Gudang Cikupa', NULL, 61, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(30, NULL, NULL, NULL, 'EBA - Medicine', NULL, 62, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(31, NULL, NULL, NULL, 'EBA - Others', NULL, 63, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(32, NULL, NULL, NULL, 'EBA - Packaging', NULL, 64, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(33, NULL, NULL, NULL, 'EBA - Sparepart', NULL, 65, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(34, NULL, NULL, NULL, 'EBA - Storage ABF', NULL, 66, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(35, NULL, NULL, NULL, 'EBA - Storage Expedisi', NULL, 67, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(36, NULL, NULL, NULL, 'EBA - Storage External', NULL, 68, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(37, NULL, NULL, NULL, 'EBA - Storage Live Bird', NULL, 69, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(38, NULL, NULL, NULL, 'EBA - Storage Produksi (WIP)', NULL, 70, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(39, NULL, NULL, NULL, 'EBA - Storage Retur', NULL, 71, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(40, NULL, NULL, NULL, 'EBA - Storage Susut', NULL, 72, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL),
(41, NULL, NULL, NULL, 'EBA NONE', NULL, 33, NULL, '2021-08-23 07:42:12', '2021-08-23 07:42:12', NULL, NULL);

INSERT INTO `driver` (`id`, `nama` , `no_polisi`) VALUES (1, 'P Dadang', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (2, 'Komeng', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (3, 'Darmin', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (4, 'Hasan', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (5, 'Hadi', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (6, 'Apit', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (7, 'P Ujang', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (8, 'Ook', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (9, 'Andri', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (10, 'Dutik', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (11, 'Kacip', '');
INSERT INTO `driver` (`id`, `nama`, `no_polisi`) VALUES (12, 'Paino', '');


INSERT INTO `user_role` (`id`, `function_name`, `function_desc`, `menu_order`, `status`, `key`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'page-purchasing', 'Purchasing', 17, 1, NULL, NULL, NULL, NULL),
(2, 'page-security', 'Security', 2, 1, NULL, NULL, NULL, NULL),
(3, 'page-lpah', 'LPAH', 3, 1, NULL, NULL, NULL, NULL),
(4, 'page-qc', 'QC', 4, 1, NULL, NULL, NULL, NULL),
(5, 'page-grading', 'Grading', 5, 1, NULL, NULL, NULL, NULL),
(6, 'page-evis', 'Evis', 7, 1, NULL, NULL, NULL, NULL),
(7, 'page-kepala-produksi', 'Kepala Produksi', 8, 1, NULL, NULL, NULL, NULL),
(8, 'page-kepala-regu-boneless', 'Kepala Regu Boneless', 10, 1, NULL, NULL, NULL, NULL),
(9, 'page-kepala-regu-parting', 'Kepala Regu Parting', 11, 1, NULL, NULL, NULL, NULL),
(10, 'page-kepala-regu-parting-marinasi', 'Kepala Regu Parting Marinasi', 13, 1, NULL, NULL, NULL, NULL),
(11, 'page-kepala-regu-whole-chicken', 'Kepala Regu Whole Chicken', 14, 1, NULL, NULL, NULL, NULL),
(12, 'page-kepala-regu-frozen', 'Kepala Regu Frozen', 15, 1, NULL, NULL, NULL, NULL),
(13, 'page-chiller', 'Chiller', 16, 1, NULL, NULL, NULL, NULL),
(14, 'page-hasil-produksi', 'Hasil Produksi', 1, 1, NULL, NULL, NULL, NULL),
(15, 'page-warehouse', 'Warehouse', 17, 1, NULL, NULL, NULL, NULL),
(16, 'page-abf', 'Abf', 18, 1, NULL, NULL, NULL, NULL),
(17, 'page-item', 'Item', 19, 1, NULL, NULL, NULL, NULL),
(18, 'page-supplier', 'Supplier', 20, 1, NULL, NULL, NULL, NULL),
(19, 'page-driver', 'Driver', 21, 1, NULL, NULL, NULL, NULL),
(20, 'page-customer', 'Customer', 22, 1, NULL, NULL, NULL, NULL),
(21, 'page-admin', 'Admin', 23, 1, NULL, NULL, NULL, NULL),
(22, 'page-laporan', 'Laporan', 24, 1, NULL, NULL, NULL, NULL),
(23, 'page-salesorder', 'Sales order', 25, 1, NULL, NULL, NULL, NULL),
(24, 'page-ppic', 'PPIC', 26, 1, NULL, NULL, NULL, NULL);



INSERT INTO `options` (`id`, `icon`, `slug`, `option_type`, `position`, `option_name`, `option_value`, `data`, `editable`, `menu_order`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 'jumlah_keranjang', NULL, NULL, 'Keranjang', '7', NULL, 0, NULL, 1, NULL, '2021-04-29 04:58:32', '2021-04-29 04:58:32'),
(2, NULL, 'berat_keranjang', NULL, NULL, 'Berat Keranjang', '2', NULL, 0, NULL, 1, NULL, '2021-04-29 04:59:37', '2021-04-29 05:34:44'),
(3, NULL, 'sync_url', NULL, NULL, 'Sync Url', 'https://muhhusniaziz.com/cgl/sync.php', NULL, 0, NULL, 1, NULL, '2021-04-29 04:59:37', '2021-04-29 05:34:44'),
(4, NULL, 'cloud_url', NULL, NULL, 'Cloud Url', 'https://cgl.cyberolympus.com', NULL, 0, NULL, 1, NULL, '2021-04-29 04:59:37', '2021-04-29 05:34:44');


INSERT INTO `users`(`id`, `name`, `email`, `email_verified_at`, `company_id`, `password`, `pin`, `phone`, `phone_verified_at`, `account_type`, `account_role`, `group_role`, `photo`, `last_login`, `fcm_token`, `remember_token`, `created_at`, `updated_at`, `status`, `key`, `deleted_at`) VALUES
(4, 'security', 'security@cgl.com', NULL, NULL, '$2y$10$vYReFGK8mJ17s1Z3zyAlBuQme9ENA3Fi7dwMbKEdzX31K.MVsZ6PW', NULL, NULL, NULL, '1', 'admin', '2', NULL, NULL, NULL, NULL, '2021-04-29 08:02:13', '2021-04-29 08:04:17', 1, NULL, NULL),
(5, 'lpah', 'lpah@cgl.com', NULL, NULL, '$2y$10$JLcHaSaxEgu93I7y/0kDkeIps1CXkF5/0R6VTavibMIPF5DSgy37e', NULL, NULL, NULL, '1', 'admin', '3', NULL, NULL, NULL, NULL, '2021-04-29 08:02:28', '2021-04-29 08:04:23', 1, NULL, NULL),
(6, 'qc', 'qc@cgl.com', NULL, NULL, '$2y$10$6NATzGT4FgSmBaeLJ/.mTOhSOU/x9vwAUcmDgFO28oDoUGypNtNcC', NULL, NULL, NULL, '1', 'admin', '4', NULL, NULL, NULL, NULL, '2021-04-29 08:02:38', '2021-04-29 08:04:28', 1, NULL, NULL),
(7, 'lpps', 'lpps@cgl.com', NULL, NULL, '$2y$10$Q9iD9tsgZFLbKVUPsr5lie9eU.uhZPxR04BEKZTc5ZkYyshxNjNpC', NULL, NULL, NULL, '1', 'admin', '5', NULL, NULL, NULL, NULL, '2021-04-29 08:02:56', '2021-04-29 08:04:32', 1, NULL, NULL),
(8, 'chiller', 'chiller@cgl.com', NULL, NULL, '$2y$10$jxTJ4iZAclWr1VWkPX.F5O0InamKMRbvM6m3.gPLe2nF0ONoxbVTi', NULL, NULL, NULL, '1', 'admin', '13', NULL, NULL, NULL, NULL, '2021-04-29 08:03:52', '2021-04-29 08:04:41', 1, NULL, NULL),
(9, 'evis', 'evis@cgl.com', NULL, NULL, '$2y$10$njV4qQxqs46ur5.Gu/PMguuyAqtENsM3e.pbjGivaMchp5ZeqFKTa', NULL, NULL, NULL, '1', 'admin', '6', NULL, NULL, NULL, NULL, '2021-04-29 08:04:07', '2021-04-29 08:04:46', 1, NULL, NULL),
(10, 'hasil produksi', 'hasilproduksi@cgl.com', NULL, NULL, '$2y$10$Fs6YVN46vzxJ.ZXHVA023uc/NdaLWyTE6M6ILKuCYEu5bMjiKy.ey', NULL, NULL, NULL, '1', 'admin', '14', NULL, NULL, NULL, NULL, '2021-04-29 08:05:13', '2021-04-29 08:05:20', 1, NULL, NULL),
(11, 'gudang', 'gudang@cgl.com', NULL, NULL, '$2y$10$puBUNHAO9n0lGBGVv.gxv.BqxTThG0q4lmtPqA6q.Hj2GL4fuiFnW', NULL, NULL, NULL, '1', 'admin', '15', NULL, NULL, NULL, NULL, '2021-04-29 08:05:35', '2021-04-29 08:06:40', 1, NULL, NULL),
(12, 'abf', 'abf@cgl.com', NULL, NULL, '$2y$10$uFX4ejOA6.FoiZ4eVmccRuX9R.NUjVo7854rdfiraDfgtrYiTMvJe', NULL, NULL, NULL, '1', 'admin', '16', NULL, NULL, NULL, NULL, '2021-04-29 08:05:48', '2021-04-29 08:06:46', 1, NULL, NULL),
(13, 'kepala produksi', 'kepalaproduksi@cgl.com', NULL, NULL, '$2y$10$wrlOpz0XpnT590aKpGT7qOXkhSpPpX9ucqzY6qWKv0yFbaIv2MEbu', NULL, NULL, NULL, '1', 'admin', '7', NULL, NULL, NULL, NULL, '2021-04-29 08:06:07', '2021-04-29 08:06:54', 1, NULL, NULL),
(14, 'kepala regu', 'kepalaregu@cgl.com', NULL, NULL, '$2y$10$3X8ge4JoFBIMEG4yXF6NNOg1AJnt9DRJXqTySHFhr55Sy7icWON1W', NULL, NULL, NULL, '1', 'admin', '8,9,10,11,12', NULL, NULL, NULL, NULL, '2021-04-29 08:06:35', '2021-04-29 08:07:03', 1, NULL, NULL),
(15, 'purchasing', 'purchasing@cgl.com', NULL, NULL, '$2y$10$FJNehPvOWyLLQ7uYOyWFKeX5xeeJO20KRuPK0fgyqrwQTE8aC49s2', NULL, NULL, NULL, '1', 'admin', '1', NULL, NULL, NULL, NULL, '2021-04-30 07:56:59', '2021-04-30 07:57:07', 1, NULL, NULL),
(16, 'ppic', 'ppic@cgl.com', NULL, NULL, '$2y$10$YZMSsmD7141GA51fDanRgeBZG.e0vf8hDSoAVraY.H/VYWHE/9SRa', NULL, NULL, NULL, '1', 'admin', '1', NULL, NULL, NULL, NULL, '2021-04-30 07:56:59', '2021-04-30 07:57:07', 1, NULL, NULL);

INSERT INTO `user_role` (`id`, `function_name`, `function_desc`, `menu_order`, `status`, `key`, `created_at`, `updated_at`, `deleted_at`) VALUES (25, 'page-netsuite', 'Netsuite', 27, 1, NULL, NULL, NULL, NULL);
INSERT INTO `users`(`id`, `name`, `email`, `email_verified_at`, `company_id`, `password`, `pin`, `phone`, `phone_verified_at`, `account_type`, `account_role`, `group_role`, `photo`, `last_login`, `fcm_token`, `remember_token`, `created_at`, `updated_at`, `status`, `key`, `deleted_at`) VALUES (17, 'netsiote', 'admin@netsuite.com', NULL, NULL, '$2y$10$RP4ne57vpBeLwh9AbbAoHecv5qe82Fch4xs2ZBHxh0Ta2Yuk2Tvfm', NULL, NULL, NULL, '1', 'admin', '25', NULL, NULL, NULL, NULL, '2021-04-30 07:56:59', '2021-04-30 07:57:07', 1, NULL, NULL);

INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8908 ZF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8311 ZF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8043 VA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8614 ZN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8027 ZC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8246 VX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8043 VA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8259 XA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8469 ZN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8687 ZD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8258 XA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8310 ZF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8258 XA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8028 ZC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8685 ZD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8310 ZF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8906 ZF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'B 9932 BCC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'F 8437 TD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8044 VA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8245 VX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'F 8561 SR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8726 ZV', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'A 8766 ZV', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `mobil` (`id`, `alamat`, `no_polisi`, `tonase`, `kode_pos`, `driver_kirim`, `driver_exspedisi`, `status`, `key`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'S 9818 SB', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);


INSERT INTO `options` (`id`, `icon`, `slug`, `option_type`, `position`, `option_name`, `option_value`, `data`, `editable`, `menu_order`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'evis_broiler', NULL, NULL, 'Item Evis Broiler', "1211810005, 1211830001, 1211840002, 1211820005", NULL, NULL, NULL, '1', NULL, NULL, NULL);
INSERT INTO `options` (`id`, `icon`, `slug`, `option_type`, `position`, `option_name`, `option_value`, `data`, `editable`, `menu_order`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'evis_kampung', NULL, NULL, 'Item Evis Kampung', "1212810004, 1212830001, 1212840002, 1212820005", NULL, NULL, NULL, '1', NULL, NULL, NULL);
INSERT INTO `options` (`id`, `icon`, `slug`, `option_type`, `position`, `option_name`, `option_value`, `data`, `editable`, `menu_order`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'evis_pejantan', NULL, NULL, 'Item Evis Pejantan', "1213810004, 1213830001, 1213840002, 1213820005", NULL, NULL, NULL, '1', NULL, NULL, NULL);
INSERT INTO `options` (`id`, `icon`, `slug`, `option_type`, `position`, `option_name`, `option_value`, `data`, `editable`, `menu_order`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'evis_parent', NULL, NULL, 'Item Evis Parent', "1214810004, 1214830001, 1214840002, 1214820005", NULL, NULL, NULL, '1', NULL, NULL, NULL);

INSERT INTO `wilayah` (`id`, `slug`, `nama`, `parent_id`, `icon`, `longitude`, `latitude`, `status`, `urutan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Jadetabek', NULL, NULL, NULL, NULL, 1, NULL, '2020-11-20 10:41:04', '2020-11-20 10:41:04', NULL),
(2, NULL, 'Bogor', NULL, NULL, NULL, NULL, 1, NULL, '2020-11-20 10:41:22', '2020-11-20 10:41:22', NULL),
(3, NULL, 'Surabaya', NULL, NULL, NULL, NULL, 1, NULL, '2020-11-20 10:41:30', '2020-11-20 10:41:30', NULL),
(4, NULL, 'Sukabumi', NULL, NULL, NULL, NULL, 1, NULL, '2020-11-20 10:41:49', '2020-11-20 10:41:49', NULL),
(5, NULL, 'Bandung', NULL, NULL, NULL, NULL, 1, NULL, '2020-11-20 10:41:56', '2020-11-20 10:41:56', NULL);

INSERT INTO `clients` (`id`, `name`, `email`, `company_id`, `last_login`, `remember_token`, `created_at`, `updated_at`, `status`, `token`, `key`, `deleted_at`) VALUES ('1', 'netsuite', NULL, '1', '2021-07-18 04:53:19.000000', NULL, NULL, NULL, NULL, 'LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710', NULL, NULL);
