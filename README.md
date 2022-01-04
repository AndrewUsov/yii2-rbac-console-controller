# Simple RBAC controller for easiest start your work

## 1. Update your main common/config/main.php (add authManager component):

  `'authManager' => ['class' => 'yii\rbac\DbManager']` or `'authManager' => ['class' => 'yii\rbac\PhpManager']`
  
## 2. Apply migrations or create tables in db manually  

  `yii migrate --migrationPath=@yii/rbac/migrations`
  
## 3. Place RbacController.php into console/controllers

## 4. Use from terminal
