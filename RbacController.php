<?php
namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;

/**
 * Simple RBAC controller for easiest start your work
 * 
 * @author Andrew Usov <usovlive@gmail.com>
 */
class RbacController extends Controller
{

// ================================== Create ==================================

    /**
     * Create new role for RBAC
     * 
     * @param String $name Name of the new role
     * @param String $desc Description of the role
     */
    public function actionCreateRole($name, $desc) {
        if (!$this->confirm("Do you want to make role \"$name\" with description \"$desc\"\n")) echo "Canceled\n";
        $role = Yii::$app->authManager->createRole($name);
        $role->description = $desc;
        Yii::$app->authManager->add($role); 
        echo "Done!\n";
        return;
    }

    /**
     * Create new permission
     * 
     * @param String $name Name of the new permission
     * @param String $desc Description of the permission
     */
    public function actionCreatePermission($name, $desc) {
        if (!$this->confirm("Do you want to make permission \"$name\" with description \"$desc\"\n")) echo "Canceled\n";
        $permission = Yii::$app->authManager->createPermission($name);
        $permission->description = $desc;
        Yii::$app->authManager->add($permission); 
        echo "Done!\n";
        return;
    }

// ================================= Assignment ================================

    /**
     * Assign role to user
     * 
     * @param Integer $userId The users id for assignment to
     * @param String $roleName The role which will assignment
     */
    public function actionAssignRoleToUser($userId, $roleName) {
        if (!$this->confirm("Do you want to assign role \"$roleName\" to user with id \"$userId\"\n")) echo "Canceled\n";
        $role = Yii::$app->authManager->getRole($roleName);
        if ($role == NULL) { echo "Role \"$roleName\" is not exist!\n"; return; }
        $user = User::findIdentityWithAnyStatus($userId);
        if ($user == NULL) { echo "User with id $userId is not found\n"; return; }
        Yii::$app->authManager->assign($role, $user->id);
        echo "Done!\n";
        return;
    }

    /**
     * Assign permission to user
     * 
     * @param Integer $userId The users id for assignment to
     * @param String $permissionName The permission which will assignment
     */
    public function actionAssignPermissionToUser($userId, $permissionName) {
        if (!$this->confirm("Do you want to assign permission \"$permissionName\" to user with id \"$userId\"\n")) echo "Canceled\n";
        $permission = Yii::$app->authManager->getPermission($permissionName);
        if ($permission == NULL) { echo "Permission \"$permissionName\" is not exist!\n"; return; }
        $user = User::findIdentityWithAnyStatus($userId);
        if ($user == NULL) { echo "User with id $userId is not found\n"; return; }
        Yii::$app->authManager->assign($permission, $user->id);
        echo "Done!\n";
        return;
    }

// ================================== Get info ==================================

    /**
     * Return all permissions in the system
     */
    public function actionGetAllPermissions() {
        $permissions = Yii::$app->authManager->getPermissions();
        echo count($permissions) . " founded in the system:\n";
        foreach ($permissions as $permission) {
            echo "- " . $permission->name . " (" . $permission->description . ")\n";
        }
        echo "Done!\n";
        return;
    }

    /**
     * Return all roles in the system
     */
    public function actionGetAllRoles() {
        $roles = Yii::$app->authManager->getRoles();
        echo count($roles) . " founded in the system:\n";
        foreach ($roles as $role) {
            echo "- " . $role->name . " (" . $role->description . ")\n";
        }
        echo "Done!\n";
        return;
    }

    /**
     * Return all roles by user
     * 
     * @param Integer $userId The user id whose roles you need
     */
    public function actionGetRolesByUser($userId) {
        $user = User::findIdentityWithAnyStatus($userId);
        if ($user == NULL) { echo "User with id $userId is not found\n"; return; }
        $roles = Yii::$app->authManager->getRolesByUser($user->id);
        echo "Founded " . count($roles) . " roles:\n";
        foreach ($roles as $role) {
            echo "- " . $role->name . "\n";
        }
        echo "Done!\n";
        return;
    }

    /**
     * Return all permissions by user
     * 
     * @param Integer $userId The user id whose permissions you need
     */
    public function actionGetPermissionsByUser($userId) {
        $user = User::findIdentityWithAnyStatus($userId);
        if ($user == NULL) { echo "User with id $userId is not found\n"; return; }
        $permissions = Yii::$app->authManager->getPermissionsByUser($user->id);
        echo "Founded " . count($permissions) . " roles:\n";
        foreach ($permissions as $permission) {
            echo "- " . $permission->name . "\n";
        }
        echo "Done!\n";
        return;
    }

    /**
     * Return all permissions by role
     * 
     * @param String $roleName The role whose permissions you need
     */
    public function actionGetPermissionsByRole($roleName) {
        $role = Yii::$app->authManager->getRole($roleName);
        if ($role == NULL) { echo "Role \"$roleName\" is not exist!\n"; return; }
        $permissions = Yii::$app->authManager->getPermissionsByRole($roleName);
        echo "Founded " . count($permissions) . " roles:\n";
        foreach ($permissions as $permission) {
            echo "- " . $permission->name . "\n";
        }
        echo "Done!\n";
        return;
    }

// ================================== Inherit ==================================

    /**
     * Inherit one role from another
     * 
     * @param String $toRole The role that should inherit the another role
     * @param String $fromRole The role which will inherited
     */
    public function actionInheritRoleRole($toRole, $fromRole) {
        if (!$this->confirm("Are you want to inherit role \"$fromRole\" to role \"$toRole\"\n")) echo "Canceled\n";
        $roleFrom = Yii::$app->authManager->getRole($fromRole);
        if ($roleFrom == NULL) { echo "Role \"$fromRole\" is not exist!\n"; return; }
        $roleTo = Yii::$app->authManager->getRole($toRole);
        if ($roleTo == NULL) { echo "Role \"$toRole\" is not exist!\n"; return; }
        Yii::$app->authManager->addChild($roleTo, $roleFrom);
        echo "Done!\n";
        return;
    }

    /**
     * Inherit permision to role
     * 
     * @param String $toRole The role that should inherit the permission
     * @param String $fromPermission The permission which will inherited
     */
    public function actionInheritRolePermission($toRole, $fromPermission) {
        if (!$this->confirm("Are you want to inherit permission \"$fromPermission\" to role \"$toRole\"\n")) echo "Canceled\n";
        $permissionFrom = Yii::$app->authManager->getRole($fromPermission);
        if ($permissionFrom == NULL) { echo "Permission \"$fromPermission\" is not exist!\n"; return; }
        $roleTo = Yii::$app->authManager->getRole($toRole);
        if ($roleTo == NULL) { echo "Role \"$toRole\" is not exist!\n"; return; }
        Yii::$app->authManager->addChild($roleTo, $permissionFrom);
        echo "Done!\n";
        return;
    }

    /**
     * Inherit role to permission
     * 
     * @param String $toPermission The permission that should inherit the role
     * @param String $fromRole The role which will inherited
     */
    public function actionInheritPermissionRole($toPermission, $fromRole) {
        if (!$this->confirm("Are you want to inherit role \"$fromRole\" to permission \"$toPermission\"\n")) echo "Canceled\n";
        $roleFrom = Yii::$app->authManager->getRole($fromRole);
        if ($roleFrom == NULL) { echo "Role \"$fromRole\" is not exist!\n"; return; }
        $permissionTo = Yii::$app->authManager->getRole($toPermission);
        if ($permissionTo == NULL) { echo "Permission \"$toPermission\" is not exist!\n"; return; }
        Yii::$app->authManager->addChild($permissionTo, $roleFrom);
        echo "Done!\n";
        return;
    }

    /**
     * Inherit permission to permission
     * 
     * @param String $toPermission The permission that should inherit another permission
     * @param String $fromPermission The permission which will inherited
     */
    public function actionInheritPermissionPermission($toPermission, $fromPermission) {
        if (!$this->confirm("Are you want to inherit permission \"$fromPermission\" to permission \"$toPermission\"\n")) echo "Canceled\n";
        $permissionFrom = Yii::$app->authManager->getRole($fromPermission);
        if ($permissionFrom == NULL) { echo "Permission \"$fromPermission\" is not exist!\n"; return; }
        $permissionTo = Yii::$app->authManager->getRole($toPermission);
        if ($permissionTo == NULL) { echo "Permission \"$toPermission\" is not exist!\n"; return; }
        Yii::$app->authManager->addChild($permissionTo, $permissionFrom);
        echo "Done!\n";
        return;
    }

}