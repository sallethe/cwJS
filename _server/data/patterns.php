<?php

class HtmlPatterns
{
    public static string $namePattern = '^[a-zA-Z\- ]{2,50}$';
    public static string $idPattern = '^[a-zA-Z0-9]{5,20}$';
    public static string $pwdPattern = '^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$';
}

class PhpPatterns
{
    public static string $namePattern = '/^[a-zA-Z\- ]{2,50}$/';
    public static string $idPattern = '/^[a-zA-Z0-9]{5,20}$/';
    public static string $pwdPattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
}
