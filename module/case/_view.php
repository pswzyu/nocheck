<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

protect();

// get the case information by caseid and then display it
$udb->query("SELECT * FROM `nocheck_cases` WHERE id=".$caseid);

$table = $udb->fetch_assoc();

$VisaType = Enums::$enum_visatype[$table["VisaType"]];
$VisaEntry = Enums::$enum_visaentry[$table["VisaEntry"]];
$Consulate = Enums::$enum_consulate[$table["Consulate"]];
$Degree = Enums::$enum_degree[$table["Degree"]];
$ApplicationStatus = Enums::$enum_status[$table["ApplicationStatus"]];

?>

<div id="main_table" >
    <table border="1">
        <tr><th>DS-160 Case ID</th><th>Username</th><th>First Name</th><th>Last Name</th>
        </tr>
        <?php
            echo "<tr>
                <td>-</td><td>{$table["Nickname"]}</td>
                <td>{$table["FirstName"]}</td><td>{$table["LastName"]}</td>
            </tr>";
        ?>
    </table>
    
    <table border="1">
        <tr><th>Visa Type</th><th>Visa Entry</th><th>US Consulate</th><th>Years In USA</th>
            <th>Citizenship</th>
        </tr>
        <?php
            echo "<tr>
                <td>{$VisaType}</td><td>{$VisaEntry}</td>
                <td>{$Consulate}</td><td>{$table["YearsInUSA"]}</td>
                <td>{$table["Citizenship"]}</td>
            </tr>";
        ?>
    </table>
    
    <table border="1">
        <tr><th>University(College)</th><th>Degree</th><th>Major</th><th>Employer</th>
            <th>Job Title</th>
        </tr>
        <?php
            echo "<tr>
                <td>{$table["University"]}</td><td>{$Degree}</td>
                <td>{$table["Major_old"]}</td><td>{$table["Employer"]}</td>
                <td>{$table["JobTitle"]}</td>
            </tr>";
        ?>
    </table>
    
    <table border="1">
        <tr><th>Visa Status</th><th>Apply Date</th><th>Issued Data</th>
        </tr>
        <?php
            echo "<tr>
                <td>{$ApplicationStatus}</td><td>{$table["ApplicationDate"]}</td>
                <td>{$table["ClearanceDate"]}</td>
            </tr>";
        ?>
    </table>
    
</div>