<!-- Begin Main Menu -->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(5, "mi_userlevelpermissions", $Language->MenuPhrase("5", "MenuText"), "userlevelpermissionslist.php", -1, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(6, "mi_userlevels", $Language->MenuPhrase("6", "MenuText"), "userlevelslist.php", -1, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(7, "mi_batches", $Language->MenuPhrase("7", "MenuText"), "batcheslist.php", -1, "", AllowListMenu('{163802B9-268A-4AFB-8FD6-7A7D18262A99}batches'), FALSE);
$RootMenu->AddMenuItem(1, "mi_deductions", $Language->MenuPhrase("1", "MenuText"), "deductionslist.php", -1, "", AllowListMenu('{163802B9-268A-4AFB-8FD6-7A7D18262A99}deductions'), FALSE);
$RootMenu->AddMenuItem(2, "mi_accounts", $Language->MenuPhrase("2", "MenuText"), "accountslist.php", -1, "", AllowListMenu('{163802B9-268A-4AFB-8FD6-7A7D18262A99}accounts'), FALSE);
$RootMenu->AddMenuItem(3, "mi_banks", $Language->MenuPhrase("3", "MenuText"), "bankslist.php", -1, "", AllowListMenu('{163802B9-268A-4AFB-8FD6-7A7D18262A99}banks'), FALSE);
$RootMenu->AddMenuItem(4, "mi_emp", $Language->MenuPhrase("4", "MenuText"), "emplist.php", -1, "", AllowListMenu('{163802B9-268A-4AFB-8FD6-7A7D18262A99}emp'), FALSE);
$RootMenu->AddMenuItem(-1, "mi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
