<?php

$conn = mysqli_connect(
"localhost",
"root",
"",
"fermentasi_tape"
);

function newtonInterpolation(
$x0,$x1,$x2,
$y0,$y1,$y2,
$x
){

$b0 = $y0;

$b1 = ($y1-$y0)/($x1-$x0);

$f21 = ($y2-$y1)/($x2-$x1);

$b2 = ($f21-$b1)/($x2-$x0);

return
$b0 +
$b1*($x-$x0) +
$b2*($x-$x0)*($x-$x1);
}

$hasilSuhu = "";
$hasilAlkohol = "";

if(isset($_POST['prediksi'])){

$jam = $_POST['jam'];

$hasilSuhu =
newtonInterpolation(
10,11,12,
30.2,31.5,31.8,
$jam
);

$hasilAlkohol =
newtonInterpolation(
10,11,12,
1.2,1.45,1.8,
$jam
);
}

$data =
mysqli_query(
$conn,
"SELECT * FROM data_sensor"
);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>
Dashboard Monitoring Fermentasi
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
background:#f4f7fc;
}

.sidebar{
height:100vh;
background:#1e293b;
color:white;
padding:20px;
}

.sidebar h3{
font-weight:bold;
}

.card-custom{
border:none;
border-radius:20px;
box-shadow:0 4px 20px rgba(0,0,0,.1);
}

.stat{
font-size:32px;
font-weight:bold;
}

.title{
font-weight:700;
margin-bottom:20px;
}

</style>

</head>

<body>

<div class="container-fluid">

<div class="row">

<div class="col-md-2 sidebar">

<h3>Fermentasi</h3>

<hr>

<p>Dashboard</p>
<p>Interpolasi Newton</p>
<p>Gauss Seidel</p>

</div>

<div class="col-md-10 p-4">

<h2 class="title">
Monitoring Fermentasi Tape Ketan
</h2>

<div class="row">

<div class="col-md-4">

<div class="card card-custom p-3">

<h6>Efisiensi RTOS</h6>

<div class="stat text-success">
98.53%
</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-custom p-3">

<h6>Efisiensi Non RTOS</h6>

<div class="stat text-danger">
79.02%
</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-custom p-3">

<h6>Selisih Efisiensi</h6>

<div class="stat text-primary">
19.51%
</div>

</div>

</div>

</div>

<br>

<div class="card card-custom p-4">

<h4>Grafik Sensor</h4>

<canvas id="grafik"></canvas>

</div>

<br>

<div class="row">

<div class="col-md-6">

<div class="card card-custom p-4">

<h4>Interpolasi Newton</h4>

<form method="POST">

<input
type="number"
step="0.1"
name="jam"
class="form-control"
placeholder="Masukkan Jam">

<br>

<button
name="prediksi"
class="btn btn-primary">

Prediksi

</button>

</form>

<hr>

<h5>

Estimasi Suhu :

<?=
$hasilSuhu ?
round($hasilSuhu,4)." °C"
:
"-"
?>

</h5>

<h5>

Estimasi Alkohol :

<?=
$hasilAlkohol ?
round($hasilAlkohol,4)." %"
:
"-"
?>

</h5>

</div>

</div>

<div class="col-md-6">

<div class="card card-custom p-4">

<h4>Gauss-Seidel</h4>

<table class="table">

<tr>

<th>Iterasi</th>
<th>X1</th>
<th>X2</th>
<th>X3</th>

</tr>

<?php

$x1=0;
$x2=0;
$x3=0;

for($i=1;$i<=4;$i++){

$x1=
(100-(2*$x2)-$x3)
/
98.53;

$x2=
(100-$x1-(2*$x3))
/
98.53;

$x3=
(100-(2*$x1)-$x2)
/
98.53;

echo "

<tr>

<td>$i</td>

<td>".round($x1,6)."</td>

<td>".round($x2,6)."</td>

<td>".round($x3,6)."</td>

</tr>

";

}

?>

</table>

</div>

</div>

</div>

<br>

<div class="card card-custom p-4">

<h4>Data Sensor</h4>

<table class="table table-striped">

<tr>

<th>ID</th>
<th>Jam</th>
<th>Suhu</th>
<th>Alkohol</th>

</tr>

<?php

mysqli_data_seek($data,0);

while(
$row =
mysqli_fetch_assoc($data)
){

?>

<tr>

<td><?= $row['id'] ?></td>
<td><?= $row['jam'] ?></td>
<td><?= $row['suhu'] ?></td>
<td><?= $row['alkohol'] ?></td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</div>

<script>

const ctx =
document.getElementById('grafik');

new Chart(ctx,{

type:'line',

data:{

labels:[
10,
11,
12
],

datasets:[

{
label:'Suhu (°C)',
data:[
30.2,
31.5,
31.8
],
borderWidth:3,
tension:.4
},

{
label:'Alkohol (%)',
data:[
1.2,
1.45,
1.8
],
borderWidth:3,
tension:.4
}

]

}

});

</script>

</body>
</html>