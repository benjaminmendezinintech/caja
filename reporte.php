<?php

ob_start();
?>

<?php
session_start();
date_default_timezone_set('America/Mexico_City');
require_once 'includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user_info = $_SESSION['user_info'];
$movimientos = [];
$nombre_completo = $user_info['NOM_TERCERO'] . ' ' . $user_info['APE_PATERNO'] . ' ' . $user_info['APE_MATERNO'];
$saldo_total = 0;
$suma_depositos = 0;
$suma_retiros = 0;
$sumatorias_por_tipo = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" || $user_info['COD_PERMISOS'] != '99') {
    if ($user_info['COD_PERMISOS'] == '99') {
        $id_tercero = $_POST['id_tercero'];
        $tip_tercero = $_POST['tip_tercero'];
    } else {
        $id_tercero = $user_info['ID_TERCERO'];
        $tip_tercero = $user_info['TIP_TERCERO'];
    }

    $movimientos = get_movimientos($id_tercero, $tip_tercero);
    
    $saldo_acumulado = 0;
    foreach ($movimientos as $key => $mov) {
        $imp_retiro = floatval($mov['IMP_RETIRO']);
        $imp_deposito = floatval($mov['IMP_DEPOSITO']);
        
        $saldo_acumulado += $imp_deposito + $imp_retiro;
        $movimientos[$key]['SALDO'] = $saldo_acumulado;
        
        $suma_depositos += $imp_deposito;
        $suma_retiros += $imp_retiro;
        
        $tipo_movimiento = $mov['DESC_MOVIMIENTO'];
        if (!isset($sumatorias_por_tipo[$tipo_movimiento])) {
            $sumatorias_por_tipo[$tipo_movimiento] = 0;
        }
        $sumatorias_por_tipo[$tipo_movimiento] += $imp_deposito + $imp_retiro;
    }
    $saldo_total = $saldo_acumulado;
    
    // Añadir el concepto 'Prestamo por liquidar:' al final de las sumatorias
    $prestamo_por_liquidar = $sumatorias_por_tipo['PRESTAMO:'] ?? 0;
    $sumatorias_por_tipo['Prestamo por liquidar:'] = $prestamo_por_liquidar;
}

function formatDate($date) {
    $dateObj = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    return $dateObj->format('d-M-Y');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Movimientos - Sistema Financiero</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .row-color:nth-child(even) {
            background-color: #e6f2ff;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .sumatorias-table {
            width: 50%;
            margin-left: 0;
        }
    </style>
</head>
<body>
    <?php if ($user_info['COD_PERMISOS'] == '99'): ?>
    <form method="post">
        <label for="id_tercero">ID Tercero:</label>
        <input type="text" id="id_tercero" name="id_tercero" required><br><br>
        <label for="tip_tercero">Tipo de Tercero:</label>
        <input type="text" id="tip_tercero" name="tip_tercero" required><br><br>
        <input type="submit" value="Consultar">
    </form>
    <?php endif; ?>
    
    <?php if (!empty($movimientos)): ?>
    <h3>Estado de Cuenta</h3>
    <p><strong>Cliente:</strong> <?php echo $nombre_completo; ?></p>
    <p><strong>Periodo:</strong> <?php echo formatDate($movimientos[0]['FEC_REGISTRO']) . ' al ' . formatDate(end($movimientos)['FEC_REGISTRO']); ?></p>
    <p><strong>Fecha de emisión:</strong> <?php echo date('d-M-Y H:i:s'); ?></p>
    
    <table>
        <tr>
            <th>Fecha</th>
            <th>Concepto</th>
            <th>Retiros</th>
            <th>Depósitos</th>
            <th>Saldo</th>
        </tr>
        <?php foreach ($movimientos as $index => $mov): ?>
        <tr class="row-color">
            <td><?php echo formatDate($mov['FEC_REGISTRO']); ?></td>
            <td><?php echo $mov['DESC_MOVIMIENTO']; ?></td>
            <td class="text-right"><?php echo ($mov['IMP_RETIRO'] != 0 && $mov['IMP_RETIRO'] !== null) ? number_format($mov['IMP_RETIRO'], 2) : ''; ?></td>
            <td class="text-right"><?php echo ($mov['IMP_DEPOSITO'] != 0 && $mov['IMP_DEPOSITO'] !== null) ? number_format($mov['IMP_DEPOSITO'], 2) : ''; ?></td>
            <td class="text-right"><?php echo number_format($mov['SALDO'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h4>Sumatorias por tipo de movimiento:</h4>
    <table class="sumatorias-table">
        <tr>
            <th class="text-left">Concepto</th>
            <th class="text-right">Importe</th>
        </tr>
        <?php foreach ($sumatorias_por_tipo as $tipo => $suma): ?>
        <tr class="row-color">
            <td class="text-left"><?php echo $tipo; ?></td>
            <td class="text-right"><?php echo number_format($suma, 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <p><strong>Suma total de depósitos:</strong> $<?php echo number_format($suma_depositos, 2); ?></p>
    <p><strong>Suma total de retiros:</strong> $<?php echo number_format($suma_retiros, 2); ?></p>
    <p><strong>Saldo final:</strong> $<?php echo number_format($saldo_total, 2); ?></p>
    <?php elseif ($user_info['COD_PERMISOS'] != '99'): ?>
    <p>No se encontraron movimientos para este usuario.</p>
    <?php endif; ?>
   
</body>
</html>

<?php
$html=ob_get_clean();
require_once 'libreria/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
$dompdf = new Dompdf();

$options = $dompdf->getOptions();
$options->set(array('isRemoteEnabled' => true));
$dompdf->loadHtml($html);
$dompdf->setPaper('letter');
$dompdf->render();
$dompdf->stream("Estado_cuenta_'$nombre_completo '.pdf", array("Attachment" => true));
?>