-- Doi mat khau admin mau thanh 123456. Chon dung database dang dung trong phpMyAdmin.
-- Chi cap nhat tai khoan admin@englishcenter.vn neu tai khoan da ton tai.
UPDATE `users` SET `password` = '$2y$12$sNJK8GXLmmLISkNtnWXL9u7SlQfQBrsN0rKkH8WMiczyuYBqlDs1m', `updated_at` = CURRENT_TIMESTAMP WHERE `email` = 'admin@englishcenter.vn';
