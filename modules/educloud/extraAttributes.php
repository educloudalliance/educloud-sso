<?php
/** getRoleAttributes
  *
  * @param $smID   Social media ID, which identifies user in idP's system
  * @return $extraAttributes    Array which keeps all the attributes which we get from REST-call 
  *
**/ 
function getRoleAttributes($authmethod, $smID){
    require_once('roledb_config.php');
    // API url
    $url = $roledb_url . '/api/1/user?' . $authmethod . '=' . $smID;

    // headers and data
    $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Token ' . $api_token
    );

    // Try to connect to the REST-server
    try {
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        
        $data = json_decode($response, true);
        $database64 = base64_encode($response);
        // Add here all the attributes you want to pass
        $extraAttributes = array(
            "educloud.oid" => $data['username'], // OID is username in RoleDB
            "educloud.data" => $database64, // RoleDB JSON response base64 encoded
        );
        
        return $extraAttributes;
    } catch(Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        return NULL;
    }
}