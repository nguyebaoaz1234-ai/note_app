-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 08, 2026 lúc 11:53 AM
-- Phiên bản máy phục vụ: 10.1.38-MariaDB
-- Phiên bản PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `note_app_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `labels`
--

CREATE TABLE `labels` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `labels`
--

INSERT INTO `labels` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES
(6, 3, 'A', '2026-04-26 04:20:36', '2026-04-26 04:20:36'),
(7, 11, 'Football', '2026-05-07 01:01:15', '2026-05-07 07:35:22'),
(8, 11, 'My self', '2026-05-07 01:01:20', '2026-05-07 01:36:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `label_note`
--

CREATE TABLE `label_note` (
  `note_id` int(10) UNSIGNED NOT NULL,
  `label_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `label_note`
--

INSERT INTO `label_note` (`note_id`, `label_id`) VALUES
(37, 7),
(39, 7);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_01_01_000001_create_users_table', 1),
(2, '2026_01_01_000002_create_notes_table', 1),
(3, '2026_01_01_000003_create_note_attachments_table', 1),
(4, '2026_01_01_000004_create_labels_table', 1),
(5, '2026_01_01_000005_create_label_note_table', 1),
(6, '2026_01_01_000006_create_shared_notes_table', 1),
(7, '2026_04_24_074209_create_note_user_table', 2),
(8, '2026_04_26_061914_add_activation_fields_to_users_table', 3),
(9, '2026_04_26_081303_create_password_resets_table', 4),
(10, '2026_04_26_094541_add_avatar_to_users_table', 5),
(11, '2026_04_26_110149_add_dark_mode_to_users_table', 6),
(12, '2026_05_06_131522_add_preferences_to_users_table', 7),
(13, '2026_05_07_085055_add_pinned_at_to_notes_table', 8),
(14, '2026_05_07_092900_add_details_to_note_user_table', 9);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notes`
--

CREATE TABLE `notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `pinned_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locked_by` int(10) UNSIGNED DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `title`, `content`, `is_pinned`, `pinned_at`, `password`, `locked_by`, `locked_at`, `created_at`, `updated_at`) VALUES
(14, 1, 'Test 1', 'Test chức năng ghim ghi chú\r\ntest', 1, '2026-05-07 08:21:00', NULL, NULL, NULL, '2026-04-22 07:25:12', '2026-05-07 10:09:02'),
(15, 1, 'Test 2', 'Test chức năng lưu tự động', 1, '2026-05-07 01:59:57', NULL, NULL, NULL, '2026-04-22 07:25:26', '2026-05-07 01:59:57'),
(16, 1, 'Test 3', 'Test chức năng chèn hình ảnh', 1, '2026-05-07 01:59:55', NULL, NULL, NULL, '2026-04-22 07:25:51', '2026-05-07 01:59:55'),
(18, 1, 'Test 4', 'Test chức năng gắn nhãn và lọc ghi chú theo nhãn \r\n( Để quay lại trang chủ chỉ cần bấm vào chữ Ghi chú 💡 ở dòng trên cùng )', 1, '2026-05-07 01:59:51', NULL, NULL, NULL, '2026-04-23 02:15:08', '2026-05-07 01:59:51'),
(22, 1, 'Test 6', 'Test chức năng gửi và nhận ghi chú', 1, '2026-05-07 07:04:56', NULL, NULL, NULL, '2026-04-24 00:59:55', '2026-05-07 08:14:12'),
(23, 3, 'Test', 'Test chức năng chia sẻ và nhận ghi chú', 0, NULL, NULL, NULL, NULL, '2026-04-24 01:06:51', '2026-04-24 01:06:51'),
(24, 3, 'From AG with love', 'Matcha Latte Lover :3', 0, NULL, NULL, NULL, NULL, '2026-04-24 01:07:58', '2026-04-24 02:32:38'),
(26, 3, 'Test Collaboration and realtime modification', 'Test chức năng chỉnh sửa ghi chú được chia sẻ trong thời gian thực', 0, NULL, NULL, NULL, NULL, '2026-04-24 02:43:15', '2026-05-06 08:03:06'),
(30, 1, 'Test 5', 'Test chức năng khóa ghi chú\r\nCách kiểm tra (Test Case):\r\nCài mật khẩu: Chọn 1 ghi chú, bấm icon 🔓 -> Nhập 123. Ghi chú sẽ bị khóa.\r\n\r\nBảo vệ (Protection): F5 trang web. Nội dung ghi chú phải biến mất, thay bằng icon 🔒 to đùng. (Thành công).\r\n\r\nMở khóa: Bấm \"Mở khóa để xem\" -> Nhập 123. Nội dung hiện ra. (Thành công).\r\n\r\nĐổi mật khẩu (Change): Khi đang mở khóa, bấm icon 🔒 đỏ -> Nhập 456. Hệ thống báo \"Đã đổi mật khẩu thành công\" và khóa ghi chú lại ngay lập tức. (Thành công).', 0, NULL, NULL, NULL, NULL, '2026-04-24 05:06:29', '2026-05-07 08:20:54'),
(37, 11, 'M10', 'Messi', 0, NULL, NULL, NULL, NULL, '2026-05-07 00:59:16', '2026-05-07 01:57:28'),
(38, 11, 'M10 CR7', 'Messi Ronaldo', 0, NULL, NULL, NULL, NULL, '2026-05-07 01:00:01', '2026-05-07 08:18:36'),
(39, 11, 'AC Inter Milan', NULL, 0, NULL, NULL, NULL, NULL, '2026-05-07 01:00:27', '2026-05-07 01:57:29'),
(43, 11, '1', '1', 0, NULL, NULL, NULL, NULL, '2026-05-07 01:10:37', '2026-05-07 07:39:24'),
(44, 11, '2', '2', 0, NULL, NULL, NULL, NULL, '2026-05-07 01:10:42', '2026-05-07 07:12:31'),
(47, 1, 'Test 7', 'Test chức năng chia sẻ và chỉnh sửa ghi chú thời gian thực\r\nhello ong', 1, '2026-05-07 02:57:25', NULL, NULL, NULL, '2026-05-07 02:56:52', '2026-05-07 06:15:04'),
(66, 11, '3', '3', 0, NULL, NULL, NULL, NULL, '2026-05-07 08:10:20', '2026-05-07 08:10:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `note_attachments`
--

CREATE TABLE `note_attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `note_id` int(10) UNSIGNED NOT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `note_attachments`
--

INSERT INTO `note_attachments` (`id`, `note_id`, `file_path`, `created_at`, `updated_at`) VALUES
(5, 16, 'public/notes/JDjM2Vay6DjdDDOQPGjeOh3Tic1EAf7IQRkEKVuY.jpeg', '2026-04-22 07:25:51', '2026-04-22 07:25:51'),
(17, 37, 'public/notes/EBaLnQ326qDz2xffc21FLAcutTFZVi4NYZhBrIsv.jpeg', '2026-05-07 00:59:16', '2026-05-07 00:59:16'),
(18, 38, 'public/notes/AODPYNWYkJxcsLExbx4fLRfIQ2PqU9rGghz7Wc3u.jpeg', '2026-05-07 01:00:07', '2026-05-07 01:00:07'),
(19, 39, 'public/notes/ShWraguwuK9w8R5ksVK7lc3Rohj7FAqBbgevhAhh.jpeg', '2026-05-07 01:00:27', '2026-05-07 01:00:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `note_user`
--

CREATE TABLE `note_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `note_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `permission` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'read',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `note_user`
--

INSERT INTO `note_user` (`id`, `note_id`, `user_id`, `permission`, `created_at`, `updated_at`) VALUES
(1, 21, 1, 'read', NULL, NULL),
(8, 26, 1, 'read', NULL, NULL),
(11, 18, 5, 'read', NULL, NULL),
(12, 38, 1, 'read', NULL, '2026-05-07 08:18:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shared_notes`
--

CREATE TABLE `shared_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `note_id` int(10) UNSIGNED NOT NULL,
  `shared_with_user_id` int(10) UNSIGNED NOT NULL,
  `permission` enum('view','edit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'view',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `view_preference` enum('list','grid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'list',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activation_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dark_mode` tinyint(1) NOT NULL DEFAULT '0',
  `note_font_size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '14px',
  `note_color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#fff9c4'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `avatar`, `is_active`, `view_preference`, `remember_token`, `created_at`, `updated_at`, `activation_token`, `dark_mode`, `note_font_size`, `note_color`) VALUES
(1, 'Đào Nguyên Bảo', 'nguyebaoaz1234@gmail.com', '$2y$10$Y2dh1SOhFcEfPgwNatSixuF9LoZ5WsGC7QR4SnluSDYgqehdB7zwO', '1778173638.jpg', 1, 'list', 'ZkY4Q2WL5ssCRNheQftzIJnv3FP0XulmkEqc5dTDWXfCvxluMRbK06eQWLyP', '2026-04-22 01:34:02', '2026-05-08 01:51:15', NULL, 0, '14px', '#ffffff'),
(3, 'Đào Nguyên Bảo v2', '52400003@student.tdtu.edu.vn', '$2y$10$o3Dga5NuXh./3Payy5./se0z2SZISwidnhQhw8rpLT89Hy6H1W4DO', '1777828920.jpg', 1, 'list', 'tKGT8m8uQk4POS6gGEs8efU98q1yjmwYJLvA5CSuxZlFwuhMheZZBG8PralR', '2026-04-24 01:05:16', '2026-05-03 10:22:00', NULL, 0, '14px', '#fff9c4'),
(11, 'Đào Nguyên Bảo v3', 'nguyenbaoaz12345@gmail.com', '$2y$10$mqyXX4NTm7haCcwfA09nS.F4mugUgXGnl3TiAeSIkWrO.8sCABoPq', '1778143021.jpg', 1, 'list', 'hQrsXWAsBpj7gjRN14zaLKwK4tHCQVkd5axEoln3b9ZutbFLFaPX5xS6Jt2V', '2026-05-07 00:40:05', '2026-05-07 08:07:55', NULL, 0, '14px', '#ffffff');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `labels_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `label_note`
--
ALTER TABLE `label_note`
  ADD PRIMARY KEY (`note_id`,`label_id`),
  ADD KEY `label_note_label_id_foreign` (`label_id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notes_user_id_foreign` (`user_id`),
  ADD KEY `notes_locked_by_foreign` (`locked_by`);

--
-- Chỉ mục cho bảng `note_attachments`
--
ALTER TABLE `note_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_attachments_note_id_foreign` (`note_id`);

--
-- Chỉ mục cho bảng `note_user`
--
ALTER TABLE `note_user`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Chỉ mục cho bảng `shared_notes`
--
ALTER TABLE `shared_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shared_notes_note_id_shared_with_user_id_unique` (`note_id`,`shared_with_user_id`),
  ADD KEY `shared_notes_shared_with_user_id_foreign` (`shared_with_user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `labels`
--
ALTER TABLE `labels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT cho bảng `note_attachments`
--
ALTER TABLE `note_attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `note_user`
--
ALTER TABLE `note_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `shared_notes`
--
ALTER TABLE `shared_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `labels`
--
ALTER TABLE `labels`
  ADD CONSTRAINT `labels_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `label_note`
--
ALTER TABLE `label_note`
  ADD CONSTRAINT `label_note_label_id_foreign` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `label_note_note_id_foreign` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_locked_by_foreign` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `note_attachments`
--
ALTER TABLE `note_attachments`
  ADD CONSTRAINT `note_attachments_note_id_foreign` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `shared_notes`
--
ALTER TABLE `shared_notes`
  ADD CONSTRAINT `shared_notes_note_id_foreign` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shared_notes_shared_with_user_id_foreign` FOREIGN KEY (`shared_with_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
