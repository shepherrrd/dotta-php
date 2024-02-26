<?php 

namespace Dotta\Enums;

// This class is used to define the environment in which the application is running (sandbox or production) classes being used instead of enums because of php support
class DottaEnvironment {
    
    const SANDBOX = 1;
    const PRODUCTION = 2;
}