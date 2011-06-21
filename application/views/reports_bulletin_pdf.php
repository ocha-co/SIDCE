<?php

$content = '
<style>

#title{
    font-size: 16px;
    font-weight:bold;
}

#description{
    text-align: justify;
    width: 400px;
    font-size: 11px;
}

#description h2{
    font-size: 20px;
}
#bulletin #map{
    width: 400px;
    height: 500px;
    border: 1px solid #cccccc;
    float: left;
    margin: 0 20px 0 20px;
}
</style>


<table>
    <tr>
        <td>
            <table>
                <tr>
                    <td valign="top">
                        <table>
                            <tr><td><img src="themes/default/img/bulletin_logo_pdf.png" /></td></tr>
                            <tr><td>'.$title.'</td></tr>
                            <tr><td>'.$copy.'</td></tr>
                        </table>
                    </td>
                    <td>'.$img_cat.'</td>
                    <td>'.$img_cities.'</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table cellspacing="20">
                <tr>
                    <td><img src="'.$img_map.'" width="400" height="500" /></td>
                    <td>
                        <table>
                            <tr><td id="description">'.$description.'</td></tr>
                            <tr><td>'.$img_trend.'</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>';

//echo $content;
$html2pdf->WriteHTML($content);
$html2pdf->Output($filename.'.pdf');


?>
