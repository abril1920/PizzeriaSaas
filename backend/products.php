<?php 
// Corrección: usar ruta relativa desde este archivo
require_once __DIR__ . '/DBConection.php';

$pdo = conectarDB();

function createProduct($name, $unit, $cost, $price) {
    global $pdo;
    $stm = $pdo->prepare("INSERT INTO products (name, unit, cost, price) VALUES (:name, :unit, :cost, :price)");
    $stm->bindParam(':name', $name);
    $stm->bindParam(':unit', $unit);
    $stm->bindParam(':cost', $cost);
    $stm->bindParam(':price', $price);
    return $stm->execute();
}

function EditProduct($id, $name, $unit, $cost, $price) {
    global $pdo;
    $stm = $pdo->prepare("UPDATE products SET name = :name, unit = :unit, cost = :cost, price = :price WHERE id = :id");
    $stm->bindParam(':id', $id);
    $stm->bindParam(':name', $name);
    $stm->bindParam(':unit', $unit);
    $stm->bindParam(':cost', $cost);
    $stm->bindParam(':price', $price);
    return $stm->execute();
}

function deleteProduct($id) {
    global $pdo;
    $stm = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stm->bindParam(':id', $id);
    return $stm->execute();
}

function getProduct($id = null) {
    global $pdo;
    if ($id === null) {
        $stm = $pdo->query("SELECT * FROM products");
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
    $stm = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stm->bindParam(':id', $id, PDO::PARAM_INT);
    $stm->execute();
    return $stm->fetch(PDO::FETCH_ASSOC);
}

function calculateTotal($cost, $unit) {
    return $cost * $unit;
}

// helper para detectar AJAX
function is_ajax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/*
 Handle POST from frontend form.
 - action: create | edit | delete
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $ok = false;
        if ($id > 0) $ok = deleteProduct($id);

        if (is_ajax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        } else {
            $redirect = '../frontend/views/DashboardMain.php';
            header('Location: ' . $redirect);
            exit;
        }
    }

    if ($action === 'edit') {
        $id    = intval($_POST['id'] ?? 0);
        $name  = trim($_POST['name'] ?? '');
        $cost  = $_POST['cost'] ?? null;
        $price = $_POST['price'] ?? null;
        $unit  = $_POST['unit'] ?? null;

        if ($id <= 0 || $name === '' || $cost === null || $price === null || $unit === null) {
            if (is_ajax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'invalid_input']);
                exit;
            } else {
                header('Location: ../frontend/views/DashboardMain.php');
                exit;
            }
        }

        $cost  = floatval($cost);
        $price = floatval($price);
        $unit  = intval($unit);

        $ok = EditProduct($id, $name, $unit, $cost, $price);

        if (is_ajax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        } else {
            header('Location: ../frontend/views/DashboardMain.php');
            exit;
        }
    }

    // default: create
    $name  = trim($_POST['name'] ?? '');
    $cost  = $_POST['cost'] ?? null;
    $price = $_POST['price'] ?? null;
    $unit  = $_POST['unit'] ?? null;

    // validación simple
    if ($name === '' || $cost === null || $price === null || $unit === null) {
        $redirect = $_SERVER['HTTP_REFERER'] ?? '../frontend/views/DashboardMain.php';
        header('Location: ' . $redirect);
        exit;
    }

    $cost  = floatval($cost);
    $price = floatval($price);
    $unit  = intval($unit);

    $ok = createProduct($name, $unit, $cost, $price);

    // Si es AJAX devolvemos JSON, si no redirigimos al dashboard
    if (is_ajax()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$ok]);
        exit;
    } else {
        $redirect = '../frontend/views/DashboardMain.php';
        header('Location: ' . $redirect);
        exit;
    }
}
?>