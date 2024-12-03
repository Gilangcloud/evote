<?php
session_start();
if(!isset($_SESSION['siswa']) || !isset($_POST['candidates'])) {
   header('location:./');
}

define('BASEPATH', dirname(__FILE__));

require('./include/connection.php');

$thn     = date('Y');
$dpn     = date('Y') + 1;
$periode = $thn.'/'.$dpn;

$candidates = $_POST['candidates'];

// Cek apakah pengguna telah memilih 10 kandidat
if (count($candidates) != 10) {
    // Jika tidak, simpan jawaban sebelumnya ke dalam session
    $_SESSION['jawaban_sebelumnya'] = $candidates;

    // Tampilkan SweetAlert2
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Alert</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    </head>
    <body>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
        <script type="text/javascript">
            Swal.fire({
                title: "Musywil PW IPM RIAU",
                text: "Silahkan Memilih 10 Kandidat!",
                icon: "warning",
                confirmButtonText: "Kembali",
            })
                .then((result) => {
                if (result.isConfirmed) {
                    history.back();
                }
            });
        </script>
    </body>
    </html>';
    exit;
}

// Update the votes for each candidate
foreach ($candidates as $candidateId) {
    $suara = 1;
    $update  = $con->prepare("UPDATE t_kandidat SET suara = suara + ? WHERE id_kandidat = ?") or die($con->error);
    $update->bind_param('is', $suara, $candidateId);
    $update->execute();
}

// Save the voter's data
$save = $con->prepare("INSERT INTO t_pemilih(nis, periode) VALUES(?,?)") or die($con->error);
$save->bind_param('ss', $_SESSION['siswa'], $periode);
$save->execute();

unset($_SESSION['siswa']);

header('location:./index.php?page=thanks');
?>