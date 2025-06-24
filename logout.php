<?php
session_start();
session_unset();
session_destroy();
redirect('index.php', 'Você saiu da sua conta.');