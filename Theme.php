<?php 
$user_key=$_SESSION['user_key'];
$themeSql="select * from  `User Dimension` inner join `Theme Dimension`  on (`User Dimension`.`User Themes`=`Theme Dimension`.`Theme Key`) where `User Key`=$user_key";

$themeResult=mysql_query($themeSql);

//print_r(mysql_fetch_array($themeResult));

if ($themeRow=mysql_fetch_array($themeResult)) 
{
$ThemeCommon=$themeRow['Theme Common Css'];
$ThemeTable=$themeRow['Theme Table Css'];
$ThemeIndex=$themeRow['Theme Index Css'];
$ThemeDropdown=$themeRow['Theme Dropdown Css'];
$ThemeCampaign=$themeRow['Theme Campaign Css'];
$background_status=$themeRow['User Theme Background Status'];
}
$bg=$user_key.".png";
if($themeRow)
{
if($background_status)
{


array_push($css_files, 'themes_css/'.$ThemeCommon.'?c='.$bg);   
array_push($css_files, 'themes_css/'.$ThemeTable);
array_push($css_files, 'themes_css/'.$ThemeIndex); 
array_push($css_files, 'themes_css/'.$ThemeDropdown);
array_push($css_files, 'themes_css/'.$ThemeCampaign);

}
else
{
array_push($css_files, 'themes_css/'.$ThemeCommon);   
array_push($css_files, 'themes_css/'.$ThemeTable);
array_push($css_files, 'themes_css/'.$ThemeIndex); 
array_push($css_files, 'themes_css/'.$ThemeDropdown);
array_push($css_files, 'themes_css/'.$ThemeCampaign);

}
}    
   

else{
array_push($css_files, 'themes_css/common.css'.'?c='.$bg); 
array_push($css_files, 'css/dropdown.css'); 
array_push($css_files, 'css/index.css');
array_push($css_files, 'table.css');
}

?> 
