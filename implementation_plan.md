# Reset Practical Class Status on Return - Implementation Plan

## Goal Description
When an Admin confirms the return of items (`terimaPengembalian`), the system should automatically remove the student from the associated Practical Class (`KelasPraktikum`). This allows the student to re-join the class or join a new one if necessary, effectively "resetting" their status for that class.

## Proposed Changes

### [Admin Controller]
#### [MODIFY] [KonfirmasiController.php](file:///c:/laragon/www/sislab%20test/sistemlaboratorium/app/Http/Controllers/Admin/KonfirmasiController.php)
- Update `terimaPengembalian` method.
- Inside the transaction, after updating the borrowing status:
    - Check if `$peminjaman->kelas_praktikum_id` is set.
    - If set, detach the user from the `kelas_praktikum_user` pivot table using `$peminjaman->user->kelasPraktikumsJoined()->detach($peminjaman->kelas_praktikum_id)`.

## Verification Plan

### Automated Verification Script
I will create a script `verify_class_reset.php` to simulate the data state and logic:
1.  Create a Dummy User and Dummy Class.
2.  Attach User to Class.
3.  Create a Peminjaman linked to the Class.
4.  Run the detachment logic (simulating the controller).
5.  Assert that the User is no longer attached to the Class.

### Manual Verification
1.  Login as a Student, join a Class.
2.  Checkout items from that Class.
3.  Login as Admin, confirm the Peminjaman.
4.  (Simulate return flow) - This might be hard to do fully manually without actual physical return flow, but we can fast-forward status in DB.
5.  Admin confirms "Terima Pengembalian".
6.  Check if Student is still in the Class (should be removed).
