-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Sep 2026 pada 08.44
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e_complain`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun_admin`
--

CREATE TABLE `akun_admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `level` enum('admin','superadmin') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `akun_admin`
--

INSERT INTO `akun_admin` (`id_admin`, `nama_admin`, `username`, `password`, `level`) VALUES
(3, 'admin1', 'admin123', 'admin123', 'superadmin'),
(4, 'admin', 'admin1', 'admin1', 'admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun_user`
--

CREATE TABLE `akun_user` (
  `id_user` int(16) NOT NULL,
  `nama_user` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `akun_user`
--

INSERT INTO `akun_user` (`id_user`, `nama_user`, `username`, `password`) VALUES
(1, 'Raka Daffa', 'rakadaffa', 'raka123');

-- --------------------------------------------------------

--
-- Struktur dari tabel `complain`
--

CREATE TABLE `complain` (
  `no_antrian` int(16) NOT NULL,
  `id_user` int(10) NOT NULL,
  `keluhan` text NOT NULL,
  `lampiran` varchar(100) NOT NULL,
  `tgl_keluhan` varchar(20) NOT NULL,
  `tjn_kategori` int(11) NOT NULL,
  `jns_kategori` int(11) NOT NULL,
  `status` enum('proses','selesai') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `faq`
--

CREATE TABLE `faq` (
  `id_faq` int(255) NOT NULL,
  `pertanyaan` text NOT NULL,
  `jawaban` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jns_keluhan`
--

CREATE TABLE `jns_keluhan` (
  `id_jenis` int(16) NOT NULL,
  `nama_jenis` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tindak_lanjut`
--

CREATE TABLE `tindak_lanjut` (
  `id_lanjut` int(16) NOT NULL,
  `no_antrian` int(10) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `tanggapan` varchar(100) DEFAULT NULL,
  `tgl_tanggapan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tjn_keluhan`
--

CREATE TABLE `tjn_keluhan` (
  `id_tujuan` int(16) NOT NULL,
  `nama_unit` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `akun_admin`
--
ALTER TABLE `akun_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `akun_user`
--
ALTER TABLE `akun_user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `complain`
--
ALTER TABLE `complain`
  ADD PRIMARY KEY (`no_antrian`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `jns_kategori` (`jns_kategori`),
  ADD KEY `tjn_kategori` (`tjn_kategori`);

--
-- Indeks untuk tabel `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id_faq`);

--
-- Indeks untuk tabel `jns_keluhan`
--
ALTER TABLE `jns_keluhan`
  ADD PRIMARY KEY (`id_jenis`);

--
-- Indeks untuk tabel `tindak_lanjut`
--
ALTER TABLE `tindak_lanjut`
  ADD PRIMARY KEY (`id_lanjut`),
  ADD KEY `no_antrian` (`no_antrian`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indeks untuk tabel `tjn_keluhan`
--
ALTER TABLE `tjn_keluhan`
  ADD PRIMARY KEY (`id_tujuan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `akun_admin`
--
ALTER TABLE `akun_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `akun_user`
--
ALTER TABLE `akun_user`
  MODIFY `id_user` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `complain`
--
ALTER TABLE `complain`
  MODIFY `no_antrian` int(16) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `faq`
--
ALTER TABLE `faq`
  MODIFY `id_faq` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jns_keluhan`
--
ALTER TABLE `jns_keluhan`
  MODIFY `id_jenis` int(16) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tindak_lanjut`
--
ALTER TABLE `tindak_lanjut`
  MODIFY `id_lanjut` int(16) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tjn_keluhan`
--
ALTER TABLE `tjn_keluhan`
  MODIFY `id_tujuan` int(16) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `complain`
--
ALTER TABLE `complain`
  ADD CONSTRAINT `complain_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `akun_user` (`id_user`),
  ADD CONSTRAINT `complain_ibfk_2` FOREIGN KEY (`jns_kategori`) REFERENCES `jns_keluhan` (`id_jenis`),
  ADD CONSTRAINT `complain_ibfk_3` FOREIGN KEY (`tjn_kategori`) REFERENCES `tjn_keluhan` (`id_tujuan`);

--
-- Ketidakleluasaan untuk tabel `tindak_lanjut`
--
ALTER TABLE `tindak_lanjut`
  ADD CONSTRAINT `tindak_lanjut_ibfk_1` FOREIGN KEY (`no_antrian`) REFERENCES `complain` (`no_antrian`),
  ADD CONSTRAINT `tindak_lanjut_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `akun_admin` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
