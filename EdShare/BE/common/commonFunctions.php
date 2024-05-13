<?php

if (!function_exists('VarExist')) {
    function VarExist($var)
    {
        if (isset($var)) {
            return $var;
        } else {
            header("location:../index.html/$var");
        }
    }
}
if (!function_exists('buildUrlWithParams')) {
    function buildUrlWithParams($baseUrl, $params)
    {
        $urlParts = parse_url($baseUrl);

        $query = isset($urlParts['query']) ? $urlParts['query'] : '';
        parse_str($query, $queryParams);

        $mergedParams = array_merge($queryParams, $params);

        $newQuery = http_build_query($mergedParams);

        $newUrl = $urlParts['path'] . '?' . $newQuery;

        return $newUrl;
    }
}
if (!function_exists('DbConnect')) {
    function DBConnect()
    {
        $dbhost = "127.0.0.1";
        $dbname = "edshare";
        $dbuser = "root";
        $dbpass = "";
        $db = null;
        try {
            $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
            $db = null;
        }
        return $db;
    }
}
if (!function_exists('getGrowthClass')) {

    function getGrowthClass($growthPercentage)
    {
        if ($growthPercentage > 0) {
            return 'text-success fw-medium';
        } elseif ($growthPercentage < 0) {
            return 'text-danger fw-medium';
        } else {
            return 'text-warning fw-medium';
        }
    }
}
if (!function_exists('getGrowthClassForArrow')) {

    function getGrowthClassForArrow($growthPercentage)
    {
        if ($growthPercentage >= 0) {
            return 'bx bx-up-arrow-alt';
        } else {
            return 'bx bx-down-arrow-alt';
        }
    }
}
if (!function_exists('formatGrowthPercentage')) {

    function formatGrowthPercentage($growthPercentage)
    {
        if ($growthPercentage > 0) {
            return "+" . $growthPercentage . "%";
        } elseif ($growthPercentage < 0) {
            return "-" . abs($growthPercentage) . "%";
        } else {
            return "+0%";
        }
    }
}
?>