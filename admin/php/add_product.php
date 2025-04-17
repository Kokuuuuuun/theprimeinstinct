<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    
    // Handle image upload
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $imagen = $_FILES['imagen'];
    $imageFileType = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;
    
    // Validate image
    $valid_types = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($imageFileType, $valid_types)) {
        echo "<script>
            alert('Solo se permiten archivos JPG, JPEG, PNG & GIF');
            window.location.href = 'tienda-admin.php';
        </script>";
        exit;
    }
    
    if (move_uploaded_file($imagen["tmp_name"], $target_file)) {
        // Save to database
        $sql = "INSERT INTO productos (nombre, descripcion, precio, img) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "ssds", $nombre, $descripcion, $precio, $target_file);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('Producto agregado correctamente');
                window.location.href = 'tienda-admin.php';
            </script>";
        } else {
            echo "<script>
                alert('Error al guardar en la base de datos');
                window.location.href = 'tienda-admin.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Error al subir la imagen');
            window.location.href = 'tienda-admin.php';
        </script>";
    }
    
    mysqli_close($connection);
}
?>