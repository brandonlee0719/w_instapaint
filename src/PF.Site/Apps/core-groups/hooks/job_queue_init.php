<?php
\Core\Queue\Manager::instance()
    ->addHandler('groups_member_join_notifications', '\Apps\PHPfox_Groups\Job\SendMemberJoinNotification')
    ->addHandler('groups_member_notifications', '\Apps\PHPfox_Groups\Job\SendMemberNotification')
    ->addHandler('groups_convert_old_group', '\Apps\PHPfox_Groups\Job\ConvertOldGroups');
