   <?php
   require_once "connection.php";

   // daftar fungsi yang boleh dipanggil lewat ?function=
   $allowed = ['getKaryawan', 'insertKaryawan', 'getKaryawanById', 'updateKaryawan', 'deleteKaryawan'];

   if (isset($_GET['function']) && in_array($_GET['function'], $allowed, true) && function_exists($_GET['function'])) {
       $_GET['function']();
   }

   // fungsi ambil data karyawan
      function getKaryawan() {
       global $connect;
       $data = [];
       $query = $connect->query("SELECT * FROM tbl_karyawan");
       while ($row = mysqli_fetch_object($query)) {
           $data[] = $row;
       }
       $response = array(
           'status' => 200,
           'message' => 'Success',
           'data' => $data
       );
       header('Content-Type: application/json');
       echo json_encode($response);
   }

   // fungsi tambah data karyawan
      function insertKaryawan() {
       global $connect;
       $check = array('id' => '', 'nama' => '', 'jenis_kelamin' => '', 'handphone' => '', 'alamat' => '');
       $check_match = count(array_intersect_key($_POST, $check));
       if ($check_match == count($check)) {
           try {
               $stmt = $connect->prepare("INSERT INTO tbl_karyawan (id, nama, jenis_kelamin, handphone, alamat) VALUES (?, ?, ?, ?, ?)");
               $stmt->bind_param("issss", $_POST['id'], $_POST['nama'], $_POST['jenis_kelamin'], $_POST['handphone'], $_POST['alamat']);
               $stmt->execute();
               $response = array(
                   'status' => 201,
                   'message' => 'Insert Success'
               );
           } catch (mysqli_sql_exception $e) {
               $response = array(
                   'status' => 400,
                   'message' => 'Insert Failed.'
               );
           }
       } else {
           $response = array(
               'status' => 405,
               'message' => 'Wrong Parameter'
           );
       }
       header('Content-Type: application/json');
       echo json_encode($response);
   }

   // fungsi ambil data karyawan by id
      function getKaryawanById() {
       global $connect;
       $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
       $stmt = $connect->prepare("SELECT * FROM tbl_karyawan WHERE id = ?");
       $stmt->bind_param("i", $id);
       $stmt->execute();
       $result = $stmt->get_result();
       $data = [];
       while ($row = mysqli_fetch_object($result)) {
           $data[] = $row;
       }
       if ($data) {
           $response = array(
               'status' => 200,
               'message' => 'Success',
               'data' => $data
           );
       } else {
           $response = array(
               'status' => 404,
               'message' => 'No Data Found'
           );
       }
       header('Content-Type: application/json');
       echo json_encode($response);
   }

   // fungsi perbarui data karyawan
      function updateKaryawan() {
       global $connect;
       $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
       $check = array('nama' => '', 'jenis_kelamin' => '', 'handphone' => '', 'alamat' => '');
       $check_match = count(array_intersect_key($_POST, $check));
       if ($check_match == count($check)) {
           try {
               $stmt = $connect->prepare("UPDATE tbl_karyawan SET nama = ?, jenis_kelamin = ?, handphone = ?, alamat = ? WHERE id = ?");
               $stmt->bind_param("ssssi", $_POST['nama'], $_POST['jenis_kelamin'], $_POST['handphone'], $_POST['alamat'], $id);
               $stmt->execute();
               $response = array(
                   'status' => 200,
                   'message' => 'Update Success'
               );
           } catch (mysqli_sql_exception $e) {
               $response = array(
                   'status' => 400,
                   'message' => 'Update Failed'
               );
           }
       } else {
           $response = array(
               'status' => 405,
               'message' => 'Wrong Parameter',
               'data' => $id
           );
       }
       header('Content-Type: application/json');
       echo json_encode($response);
   }

   // fungsi hapus data karyawan
      function deleteKaryawan() {
       global $connect;
       $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
       try {
           $stmt = $connect->prepare("DELETE FROM tbl_karyawan WHERE id = ?");
           $stmt->bind_param("i", $id);
           $stmt->execute();
           $response = array(
               'status' => 200,
               'message' => 'Delete Success'
           );
       } catch (mysqli_sql_exception $e) {
           $response = array(
               'status' => 400,
               'message' => 'Delete Fail.'
           );
       }
       header('Content-Type: application/json');
       echo json_encode($response);
   }
   ?>