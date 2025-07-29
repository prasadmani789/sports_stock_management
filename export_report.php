<?php
include 'includes/db.php';

require_once 'dompdf/vendor/autoload.php'; // Ensure path is correct
use Dompdf\Dompdf;

$reportType = $_POST['report_type'] ?? '';
$fromDate = $_POST['from'] ?? '';
$toDate = $_POST['to'] ?? '';

if (isset($_POST['export_excel'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"{$reportType}_report.xls\"");

    echo "<table border='1'>";
    echo "<tr>";

    if ($reportType === 'sales') {
        echo "<th>Date</th><th>Item Name</th><th>Qty</th><th>MRP</th><th>Total</th>";
        echo "</tr>";

        $stmt = $pdo->prepare("
            SELECT si.*, i.item_name, s.date
            FROM sale_items si
            JOIN items i ON si.item_id = i.id
            JOIN sales s ON si.sale_id = s.id
            WHERE s.date BETWEEN ? AND ?
            ORDER BY s.date DESC
        ");
        $stmt->execute([$fromDate, $toDate]);

        foreach ($stmt as $row) {
            $total = $row['quantity'] * $row['price'];
            echo "<tr>";
            echo "<td>{$row['date']}</td>";
            echo "<td>{$row['item_name']}</td>";
            echo "<td>{$row['quantity']}</td>";
            echo "<td>{$row['price']}</td>";
            echo "<td>{$total}</td>";
            echo "</tr>";
        }

    } else if ($reportType === 'purchase') {
        echo "<th>Item Name</th><th>Category</th><th>Stock</th><th>Date Added (N/A)</th>";
        echo "</tr>";

        $stmt = $pdo->prepare("
            SELECT i.*, c.name AS category_name
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
        ");
        $stmt->execute();

        foreach ($stmt as $row) {
            echo "<tr>";
            echo "<td>{$row['item_name']}</td>";
            echo "<td>{$row['category_name']}</td>";
            echo "<td>{$row['stock']}</td>";
            echo "<td>N/A</td>";
            echo "</tr>";
        }
    }

    echo "</table>";

} elseif (isset($_POST['export_pdf'])) {
    $html = "<h3>" . ucfirst($reportType) . " Report</h3>";
    $html .= "<p><strong>From:</strong> $fromDate &nbsp;&nbsp; <strong>To:</strong> $toDate</p>";
    $html .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%; border-collapse:collapse;'>";

    if ($reportType === 'sales') {
        $html .= "<thead><tr><th>Date</th><th>Item Name</th><th>Qty</th><th>MRP</th><th>Total</th></tr></thead><tbody>";

        $stmt = $pdo->prepare("
            SELECT si.*, i.item_name, s.date
            FROM sale_items si
            JOIN items i ON si.item_id = i.id
            JOIN sales s ON si.sale_id = s.id
            WHERE s.date BETWEEN ? AND ?
            ORDER BY s.date DESC
        ");
        $stmt->execute([$fromDate, $toDate]);

        foreach ($stmt as $row) {
            $total = $row['quantity'] * $row['price'];
            $html .= "<tr>
                        <td>{$row['date']}</td>
                        <td>{$row['item_name']}</td>
                        <td>{$row['quantity']}</td>
                        <td>₹{$row['price']}</td>
                        <td>₹{$total}</td>
                    </tr>";
        }

        $html .= "</tbody>";

    } else if ($reportType === 'purchase') {
        $html .= "<thead><tr><th>Item Name</th><th>Category</th><th>Stock</th><th>Date (N/A)</th></tr></thead><tbody>";

        $stmt = $pdo->prepare("
            SELECT i.*, c.name AS category_name
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
        ");
        $stmt->execute();

        foreach ($stmt as $row) {
            $html .= "<tr>
                        <td>{$row['item_name']}</td>
                        <td>{$row['category_name']}</td>
                        <td>{$row['stock']}</td>
                        <td>N/A</td>
                    </tr>";
        }

        $html .= "</tbody>";
    }

    $html .= "</table>";

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("{$reportType}_report.pdf", ["Attachment" => 1]);
    exit;
}
?>
