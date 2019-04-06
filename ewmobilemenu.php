<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(5, "mmi_userlevelpermissions", $Language->MenuPhrase("5", "MenuText"), "userlevelpermissionslist.php", -1, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(6, "mmi_userlevels", $Language->MenuPhrase("6", "MenuText"), "userlevelslist.php", -1, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(1, "mmi_deductions", $Language->MenuPhrase("1", "MenuText"), "deductionslist.php", -1, "", AllowListMenu('{163802B9-268A-4AFB-8FD6-7A7D18262A99}deductions'), FALSE);
$RootMenu->AddMenuItem(2, "mmi_accounts", $Language->MenuPhrase("2", "MenuText"), "accountslist.php", -1, "", AllowListMenu('{163802B9-268A-4AFB-8FD6-7A7D18262A99}accounts'), FALSE);
$RootMenu->AddMenuItem(3, "mmi_banks", $Language->MenuPhrase("3", "MenuText"), "bankslist.php", -1, "", AllowListMenu('{163802B9-268A-4AFB-8FD6-7A7D18262A99}banks'), FALSE);
$RootMenu->AddMenuItem(4, "mmi_emp", $Language->MenuPhrase("4", "MenuText"), "emplist.php", -1, "", AllowListMenu('{163802B9-268A-4AFB-8FD6-7A7D18262A99}emp'), FALSE);
$RootMenu->AddMenuItem(-1, "mmi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mmi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
