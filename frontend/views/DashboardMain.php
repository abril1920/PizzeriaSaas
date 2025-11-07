<?php 
    // include_once "./backend/DBConection.php";
    require_once '../../backend/DBConection.php';

    $db = conectarDB();
    $users = $db->query("SELECT * FROM users;");
    $products = $db->query("SELECT * FROM products");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/dashboard.css">
    <link rel="stylesheet" href="../styles/style.css">
    <title>Dashboard</title>
</head>
<body>
    <?php include "components/header.html"; ?>
    <div id="container1">
    <div id="continer11">   
        <h2>Lista de Productos</h2>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Unidad</th>
                <th>Costo</th>
                <th>Precio</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
            <?php foreach($products as $product){?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['unit']); ?></td>
                <td><?php echo htmlspecialchars($product['cost']); ?></td>
                <td><?php echo htmlspecialchars($product['price']); ?></td>
                <?php
                    $price = isset($product['price']) ? floatval($product['price']) : 0;
                    $unit  = isset($product['unit'])  ? intval($product['unit'])  : 0;
                    $calculatedTotal = $price * $unit;
                    // si en la BD existe la columna 'total' se usa, si no se usa el calculado
                    $displayTotal = isset($product['total']) && $product['total'] !== null && $product['total'] !== '' 
                        ? floatval($product['total']) 
                        : $calculatedTotal;
                ?>
                <td><?php echo htmlspecialchars(number_format($displayTotal, 2, '.', ',')); ?></td>
                <td>
                    <button type="button" class="btn edit edit-btn" data-id="<?php echo htmlspecialchars($product['id']); ?>">Editar</button>

                    <form class="delete-form" method="post" action="../../backend/products.php" style="display:inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <button type="button" class="btn delete delete-btn" data-id="<?php echo htmlspecialchars($product['id']); ?>">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php }?>
        </table>
        <form id="product-form" action="../../backend/products.php" method="post">
            <input type="text" name="name" placeholder="Nombre del producto" required>
            <input type="number" name="cost" placeholder="Costo del producto" required>
            <input type="number" name="price" placeholder="Precio del producto" required>
            <input type="number" name="unit" placeholder="Unidades en inventario" required>
            <button class="btn add" type="submit" >Agregar Producto</button>
        </form>
    </div>
    <div id="continer11">   
        <h2>Lista de Administradores</h2>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Edad</th>
                <th>correo</th>
            </tr>
            <?php foreach($users as $user){?>
            <tr>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['lastName']); ?></td>
                <td><?php echo htmlspecialchars($user['age']); ?></td>
                <td><?php echo htmlspecialchars($user['mail']); ?></td>  
            </tr>
            <?php }?>
        </table>
    </div>
    </div>
    <!-- add inline script just before footer include -->
<script>
(function(){
    const apiUrl = '../../backend/products.php';

    async function postAction(formData) {
        formData.append('ajax', '1');
        const res = await fetch(apiUrl, {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        });
        return res.json();
    }

    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('delete-btn')) {
            const id = e.target.dataset.id;
            if (!confirm('¿Eliminar producto?')) return;
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            postAction(fd).then(json => {
                if (json && json.success) {
                    // remove row
                    const btn = e.target;
                    const row = btn.closest('tr');
                    if (row) row.remove();
                } else {
                    alert('No se pudo eliminar.');
                }
            }).catch(()=> alert('Error de conexión.'));
        }
    });

    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('edit-btn')) {
            const btn = e.target;
            const row = btn.closest('tr');
            if (!row || row.dataset.editing === '1') return;
            row.dataset.editing = '1';

            const cells = row.querySelectorAll('td');
            const nameCell = cells[0];
            const unitCell = cells[1];
            const costCell = cells[2];
            const priceCell = cells[3];
            const actionsCell = cells[5];

            const orig = {
                name: nameCell.textContent.trim(),
                unit: unitCell.textContent.trim(),
                cost: costCell.textContent.trim(),
                price: priceCell.textContent.trim()
            };

            nameCell.innerHTML = '<input type="text" class="edit-name" value="'+orig.name+'">';
            unitCell.innerHTML = '<input type="number" class="edit-unit" value="'+orig.unit+'">';
            costCell.innerHTML = '<input type="number" step="0.01" class="edit-cost" value="'+orig.cost+'">';
            priceCell.innerHTML = '<input type="number" step="0.01" class="edit-price" value="'+orig.price+'">';

            // save / cancel buttons
            actionsCell.dataset.prev = actionsCell.innerHTML;
            actionsCell.innerHTML = '';
            const saveBtn = document.createElement('button');
            saveBtn.type = 'button';
            saveBtn.className = 'btn edit save-btn';
            saveBtn.textContent = 'Guardar';

            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn delete cancel-btn';
            cancelBtn.textContent = 'Cancelar';

            actionsCell.appendChild(saveBtn);
            actionsCell.appendChild(cancelBtn);

            // save handler
            saveBtn.addEventListener('click', function(){
                const id = btn.dataset.id;
                const fd = new FormData();
                fd.append('action','edit');
                fd.append('id', id);
                fd.append('name', row.querySelector('.edit-name').value);
                fd.append('unit', row.querySelector('.edit-unit').value);
                fd.append('cost', row.querySelector('.edit-cost').value);
                fd.append('price', row.querySelector('.edit-price').value);

                postAction(fd).then(json => {
                    if (json && json.success) {
                        // update UI: recalc total
                        const name = fd.get('name');
                        const unit = parseInt(fd.get('unit')||0,10);
                        const cost = parseFloat(fd.get('cost')||0);
                        const price = parseFloat(fd.get('price')||0);
                        nameCell.textContent = name;
                        unitCell.textContent = unit;
                        costCell.textContent = cost;
                        priceCell.textContent = price;
                        const totalCell = cells[4];
                        totalCell.textContent = (price * unit).toFixed(2);
                        actionsCell.innerHTML = actionsCell.dataset.prev;
                        row.dataset.editing = '0';
                    } else {
                        alert('No se pudo actualizar.');
                    }
                }).catch(()=> alert('Error de conexión.'));
            });

            cancelBtn.addEventListener('click', function(){
                nameCell.textContent = orig.name;
                unitCell.textContent = orig.unit;
                costCell.textContent = orig.cost;
                priceCell.textContent = orig.price;
                actionsCell.innerHTML = actionsCell.dataset.prev;
                row.dataset.editing = '0';
            });
        }
    });
})();
</script>
    <?php include "components/footer.html"; ?>
</body>
</html>