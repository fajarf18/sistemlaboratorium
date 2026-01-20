# Refactor Dosen and Modul System

- [ ] Implementation
  - [x] Migration: Schema changes (Modul user_id, Class modul_id, Drop DosenPengampu) <!-- id: 0 -->
  - [x] Models: Update relationships (User, Modul, KelasPraktikum, Peminjaman) <!-- id: 1 -->
  - [x] Controller: Create `Dosen\ModulController` <!-- id: 2 -->
  - [x] Controller: Update `Dosen\KelasPraktikumController` to use Modules <!-- id: 3 -->
  - [x] Controller: Update `KeranjangController` (checkout logic) <!-- id: 4 -->
  - [x] Views: Update Dosen Dashboard/Modul/Class views <!-- id: 5 -->
- [ ] Verification
  - [x] Verify automatic item inheritance from Module to Class <!-- id: 6 -->
  - [x] Verify checkout flow with new logic <!-- id: 7 -->
