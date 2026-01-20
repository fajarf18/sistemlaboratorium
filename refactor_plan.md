# Refactoring Dosen and Modul System - Implementation Plan

## Goal Description
Refactor the system to:
1.  Allow Lecturers (User with role 'dosen') to create Practical Modules (`Modul`).
2.  Link `Modul` to `KelasPraktikum` so classes inherit items from the module.
3.  Remove the redundant `dosen_pengampus` table and rely on the `users` table for lecturer data.

## Proposed Changes

### Database Migrations
#### [NEW] [Refactoring Migration]
-   Add `user_id` (foreign key to `users`) to `moduls` table.
-   Add `modul_id` (foreign key to `moduls`) to `kelas_praktikums` table.
-   Update `peminjamans` table: Rename/Change `dosen_pengampu_id` (FK to `dosen_pengampus`) to `dosen_id` (FK to `users`).
-   Update `keranjangs` table: Rename/Change `dosen_pengampu_id` to `dosen_id`.
-   Drop `dosen_pengampus` table.

### Models
#### [MODIFY] [Modul.php](file:///c:/laragon/www/sislab%20test/sistemlaboratorium/app/Models/Modul.php)
-   Add `user()` relationship (belongsTo User::class).

#### [MODIFY] [KelasPraktikum.php](file:///c:/laragon/www/sislab%20test/sistemlaboratorium/app/Models/KelasPraktikum.php)
-   Add `modul()` relationship.
-   Remove manual item relationship logic (if strictly using modules).

#### [MODIFY] [User.php](file:///c:/laragon/www/sislab%20test/sistemlaboratorium/app/Models/User.php)
-   Add `moduls()` relationship (hasMany Modul::class).

#### [MODIFY] [Peminjaman.php](file:///c:/laragon/www/sislab%20test/sistemlaboratorium/app/Models/Peminjaman.php)
-   Switch `dosenPengampu()` to `dosen()` (belongsTo User).

### Controllers
#### [NEW] [Dosen\ModulController.php](file:///c:/laragon/www/sislab%20test/sistemlaboratorium/app/Http/Controllers/Dosen/ModulController.php)
-   CRUD for Modul, accessible by Dosen.

#### [MODIFY] [Dosen\KelasPraktikumController.php](file:///c:/laragon/www/sislab%20test/sistemlaboratorium/app/Http/Controllers/Dosen/KelasPraktikumController.php)
-   Update store/update logic to select `modul_id`.
-   Remove logic for adding individual items if they are inherited from Modul.

#### [MODIFY] [KeranjangController.php](file:///c:/laragon/www/sislab%20test/sistemlaboratorium/app/Http/Controllers/KeranjangController.php)
-   Update checkout logic to refer to `dosen_id` (User) instead of `dosen_pengampu_id`.

## Verification Plan

### Automated Tests
1.  **Migration Test**: Verify database schema changes.
2.  **Logic Test**: Create a Dosen, Create a Modul, Create a Class using that Modul, Student joins and checks out.

### Manual Verification
1.  Login as Dosen.
2.  Create a Modul with items.
3.  Create a Class, select the Modul.
4.  Login as Student, join Class.
5.  Verify Cart is populated with items from the Modul.
6.  Checkout and verify Admin sees the correct Dosen.
