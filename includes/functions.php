<?php
require_once 'db_connection.php';



function validate_user($username, $password) {
    $conn = connect_to_database();
    $stmt = $conn->prepare("SELECT * FROM CAT_USUARIOS WHERE CORREO_USUARIO = ? OR TELEFONO_USUARIO = ? AND COD_PASS = ? AND MCA_INHABILITADO = 'N'");
    $stmt->bind_param("sss", $username,$username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user;
}


////Funcion para obtener la informacion del usuario 
function get_user_info($cod_usuario) {
    $conn = connect_to_database();
    $stmt = $conn->prepare("SELECT ctu.ID_TERCERO, ctu.TIP_TERCERO, ct.NOM_TERCERO, ct.APE_PATERNO, ct.APE_MATERNO, cu.COD_PERMISOS 
                            FROM CAT_TER_USUARIO ctu 
                            JOIN CAT_TERCEROS ct ON ctu.ID_TERCERO = ct.ID_TERCERO AND ctu.TIP_TERCERO = ct.TIP_TERCERO
                            JOIN CAT_USUARIOS cu ON ctu.COD_USUARIO = cu.COD_USUARIO
                            WHERE ctu.COD_USUARIO = ?");
    $stmt->bind_param("s", $cod_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_info = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user_info;
}



////Funcion para obtener el total de los retiros por tercero

function get_prestamo($id_tercero, $tip_tercero) {
    $conn = connect_to_database();
    $stmt = $conn->prepare("SELECT HM.FEC_REGISTRO, TM.DESC_MOVIMIENTO, HM.IMP_RETIRO,
     HM.IMP_DEPOSITO FROM HIS_MOVIMIENTOS HM JOIN CAT_TIP_MOVIMIENTOS TM ON HM.COD_MOVIMIENTO = TM.COD_MOVIMIENTO 
     WHERE HM.ID_TERCERO = ? AND HM.TIP_TERCERO = ? AND DESC_MOVIMIENTO = 'Prestamo' ORDER BY HM.FEC_REGISTRO;
    ");
    $stmt->bind_param("ss", $id_tercero, $tip_tercero);
    $stmt->execute();
    $result = $stmt->get_result();
    $prestamo = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $prestamo;
}


////Funcion para obtener todos los registros cargados por el tercero
function get_movimientos($id_tercero, $tip_tercero) {
    $conn = connect_to_database();
    $stmt = $conn->prepare("SELECT HM.FEC_REGISTRO, TM.DESC_MOVIMIENTO, HM.IMP_RETIRO, HM.IMP_DEPOSITO 
                            FROM HIS_MOVIMIENTOS HM
                            JOIN CAT_TIP_MOVIMIENTOS TM ON HM.COD_MOVIMIENTO = TM.COD_MOVIMIENTO
                            WHERE HM.ID_TERCERO = ? AND HM.TIP_TERCERO = ?
                            ORDER BY HM.FEC_REGISTRO");
    $stmt->bind_param("ss", $id_tercero, $tip_tercero);
    $stmt->execute();
    $result = $stmt->get_result();
    $movimientos = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $movimientos;
}



////Funcion para obtener los usuarios
function obtenerUsuarios($conn) {
    $sql = "SELECT ID_TERCERO,NOM_TERCERO,APE_PATERNO,APE_MATERNO
                                FROM CAT_TERCEROS";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    } else {
        return [];
    }
    $conn->close();
    return $usuarios;
}

////Funcion para obtener el estado de cuenta 

function generar_estado_cuenta_pdf($id_tercero, $tip_tercero) {
    require('fpdf/fpdf.php');

    $conn = connect_to_database();
    
    // Obtener información del tercero
    $stmt = $conn->prepare("SELECT NOM_TERCERO, APE_PATERNO, APE_MATERNO FROM CAT_TERCEROS WHERE ID_TERCERO = ? AND TIP_TERCERO = ?");
    $stmt->bind_param("ss", $id_tercero, $tip_tercero);
    $stmt->execute();
    $result = $stmt->get_result();
    $tercero = $result->fetch_assoc();
    $nombre_completo = $tercero['NOM_TERCERO'] . ' ' . $tercero['APE_PATERNO'] . ' ' . $tercero['APE_MATERNO'];

    // Obtener movimientos
    $movimientos = get_movimientos($id_tercero, $tip_tercero);

    // Crear PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'Estado de Cuenta',0,1,'C');
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,10,"Cliente: $nombre_completo",0,1);
    $pdf->Cell(0,10,"Periodo: " . $movimientos[0]['FEC_REGISTRO'] . " al " . end($movimientos)['FEC_REGISTRO'],0,1);
    $pdf->Cell(0,10,"Fecha de emisión: " . date('Y-m-d H:i:s'),0,1);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(30,10,'Fecha',1);
    $pdf->Cell(60,10,'Concepto',1);
    $pdf->Cell(30,10,'Retiros',1);
    $pdf->Cell(30,10,'Depósitos',1);
    $pdf->Cell(40,10,'Saldo',1);
    $pdf->Ln();

    $saldo = 0;
    foreach ($movimientos as $mov) {
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(30,10,$mov['FEC_REGISTRO'],1);
        $pdf->Cell(60,10,$mov['DESC_MOVIMIENTO'],1);
        $pdf->Cell(30,10,$mov['IMP_RETIRO'],1,0,'R');
        $pdf->Cell(30,10,$mov['IMP_DEPOSITO'],1,0,'R');
        $saldo += $mov['IMP_DEPOSITO'] - $mov['IMP_RETIRO'];
        $pdf->Cell(40,10,number_format($saldo, 2),1,0,'R');
        $pdf->Ln();
    }

    $pdf_file = 'estado_cuenta_' . $id_tercero . '_' . $tip_tercero . '.pdf';
    $pdf->Output('F', $pdf_file);
    $conn->close();
    return $pdf_file;
}

// FUNCION CONSULTA DEPOSITO

function deposito($id_tercero, $tip_tercero) {
    $conn = connect_to_database();
    $stmt = $conn->prepare("SELECT HM.FEC_REGISTRO, TM.DESC_MOVIMIENTO, HM.IMP_RETIRO, HM.IMP_DEPOSITO 
                            FROM HIS_MOVIMIENTOS HM JOIN CAT_TIP_MOVIMIENTOS TM ON HM.COD_MOVIMIENTO = TM.COD_MOVIMIENTO
                            WHERE HM.ID_TERCERO = ? AND HM.TIP_TERCERO = ? AND DESC_MOVIMIENTO = 'DEPOSITO AHORR.' ORDER BY HM.FEC_REGISTRO;");
    $stmt->bind_param("ss", $id_tercero, $tip_tercero);
    $stmt->execute();
    $result = $stmt->get_result();
    $deposito = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $deposito;
}




?>



