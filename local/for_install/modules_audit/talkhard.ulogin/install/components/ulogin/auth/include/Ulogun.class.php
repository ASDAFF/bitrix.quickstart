<?php


class Ulogin
{

    public static function genNickname($profile)
    {
        if (isset($profile['nickname'])) {
            return $profile['nickname'];
        } elseif (isset($profile['email']) && preg_match('/^(.+)\@/i', $profile['email'], $nickname)) {
            return $nickname[1];
        } elseif (isset($profile['first_name']) && isset($profile['last_name'])) {
            return $this->normalize(iconv('utf-8', 'windows-1251', $profile['first_name'] . ' ' . $profile['last_name']), '_');
        }

        return 'user'.rand(1000,100000);
    }

    public static function createUloginAccount($profile, $id){
        $user = new CUser;
        $ulogin_profile['EMAIL'] = $profile['EMAIL'];
        $ulogin_profile['LOGIN'] = $profile['LOGIN'];
        $ulogin_profile['PASSWORD'] = rand(1000000,10000000);
        $ulogin_profile['CONFIRM_PASSWORD'] = $ulogin_profile['PASSWORD'];
        $ulogin_profile['ACTIVE'] = 'N';
        $ulogin_profile['ADMIN_NOTES'] = $profile['NETWORK'].'='.$id;
        $ulogin_profile['EXTERNAL_AUTH_ID'] = $profile['EXTERNAL_AUTH_ID'];
        return $user->Add($ulogin_profile);
    }

    public static function updateUloginAccount($id, $new_id, $network){
        $user = new CUser;
        $user->Update($id,array('ADMIN_NOTES'=>$network.'='.$new_id));
    }

}

?>