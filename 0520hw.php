<?php

$height = 5 ;
for ($i = 1; $i <= $height; $i++){
    for ($j = 1 ;$j <= $i; $j++)
        echo "*";
    echo "<br>";
}

echo "<hr>";


for ($i = 1; $i <= $height; $i++) {
    for ($j = 1; $j <= $height - $i; $j++) {
        echo "&nbsp";  // 공백 출력
    }
    for ($d = 1; $d <= $i; $d++) {
        echo "*";  // 별 출력
    }
    echo "<br>";  // 웹 줄바꿈
}

echo "<hr>";

for ($i = 1; $i <= $height; $i++){
    for ($j = 1; $j <= $height-$i; $j++){
        echo "&nbsp";
    }
    for ($d = 1; $d <= $i*2-1; $d++){
        echo "*";}

    echo "<br>";
}
for ($i = $height-1; $i >= 1; $i--){
    for ($j = 1; $j <= $height-$i; $j++){
        echo "&nbsp";
    }
    for ($d = 1 ; $d <= $i*2-1; $d++){
        echo "*";
    }
    echo "<br>";
}


?>