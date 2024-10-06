<?php

function get_all_captures($domain) {
    $cdx_url = "http://web.archive.org/cdx/search/cdx?url={$domain}&output=json&fl=timestamp,original,mimetype,statuscode";

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $cdx_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        array_shift($data);  // Remove the first row, which is just column names
        return $data;
    } catch (Exception $e) {
        echo "Error accessing CDX API: " . $e->getMessage();
        return [];
    }
}

$domain = $_POST['domain'] ?? '';
$captures = $domain ? get_all_captures($domain) : [];
?>

<!DOCTYPE html>
<html lang="en">
<style>
    .found-message {
        color: white;
        padding: 10px;
				margin-top: 10px;
				background-color: green;
				border-radius: 5px;
				text-align:center;
				font-style: italic;
    }
</style>

<form action="" method="post">
    <input type="text" name="domain" placeholder="Enter domain" required>
    <input type="submit" value="Cek">
</form>

<?php
if ($captures) {
    echo "<div class='found-message'>" . count($captures) . " ARSIP DITEMUKAN</div>";
    echo "<table>
        <tr>
            <th>TANGGAL</th>
            <th>TIPE</th>
            <th>CEK</th>
        </tr>";

    foreach ($captures as $capture) {
        $date = DateTime::createFromFormat('YmdHis', $capture[0]);
        $formattedDate = $date->format('Y-m-d, H:i:s');
        $url = "http://web.archive.org/web/{$capture[0]}/{$capture[1]}";
        echo "<tr>
                <td>{$formattedDate}</td>
                <td>{$capture[2]}</td>
                <td><a href='{$url}' target='_blank'>CEK</a></td>
            </tr>";
    }

    echo "</table>";
}
?>
</html>
