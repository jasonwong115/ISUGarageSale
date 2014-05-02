<?php
namespace GarageSale;
/** classes/user.php
 *  The user class contains details of the current user and is used
 *  to authorize access to restricted sections of the application.
 *
 *  Many user authorization requirements are handled using session
 *  variables so the session class should be loaded before this one
 *  and the session started. 
 *
 *  This also requires database access so the database will need to be
 *  loaded prior to this class being loaded. Shouldn't be much of a
 *  problem because it will need to have been loaded for sessions.
 *
 *  How to use:
 *  $user = new user( $db_class, true|false );
 *
 *  @author Tanner Hildebrand
 */
class User {
    
    /* User level constants */
    
    /** The user level applied to all visiting, non registered or non-
     *  logged in users.
     */
    const USER_GUEST = -1;
    
	/** User level associated with users who need to verify their account */
    const USER_UNVERIFIED = 0;
	
    /** User level associated with all standard users */
    const USER_STANDARD = 1;
    
    /** User level applied to moderators */
    const USER_MODERATOR = 10;
    
    /** User level applied to managers */ 
    const USER_MANAGER = 20;
    
    /** User level applied to administrators */
    const USER_ADMIN = 30;
    
    /** User level applied to the super user */
    const USER_SUPERUSER = 40;
    
    /** Image width for user iamges */
    const IMAGE_WIDTH = 227;

    /** Image height for user iamges */
    const IMAGE_HEIGHT = 150;    
    
    /** Image resolution height for user iamges */
    const IMAGE_RESIZE_WIDTH = 400;

    /** Image resolution height for user iamges */
    const IMAGE_RESIZE_HEIGHT = 400;
    
    // private vars
    
    /** The user level decides the authorization of the user. The 
     *  lower the user level, the greater the power. 
     *  -1 (default) indicates guest.
     */
    private $user_level = -1;
    
    /** ID of this user in the database. Can be taken from session
     *  variables. 
     *  -1 (default) means guest.
     */
    private $user_id = -1;
    
    /** Username/handle of the current user. Null by default.
     */
    private $user_name = null;
    
    /** Is logged in is set to true when the user is confirmed to have
     *  logged in by the server. This value is based on the confirmation
     *  given by the session variable.
     */
    private $is_logged_in = false;
    
    /** Reference to the database class that will be used to access
     *  all of the user data. 
     */
    public $db;
    
    /** The constructor for a new user class
     *  @param $db Database class used to look up user data
     *  @param $allow_auto_login if this is true it will automatically
     *         attempt to login a user who has an appropriate session.
     */
    function __construct( $db, $allow_auto_login = true )
    {
        $this->db = $db;
        
        // datbase class should prevent from double connecting
        $this->db->connect();
        
        // see if a user is already logged in.
        if( $allow_auto_login ){
            $this->auto_login();
        }
    }
    
    
    /** Attempts to automatically log in this user if the session
     *  variables indicate that they are logged in.
     */
    function auto_login()
    {
        if( isset($_SESSION['logged_in']) ){
            $this->user_id = (int)$_SESSION['logged_in'];
            $this->do_login();
        }
    }
    
    /** Attempts to log the user into the system if they are not already
     *  If the user already has a session registered, it is renewed.
     *  If the user was not logged in and the credentials match a new
     *  session is started.
     *  @param $username unique identifier for user login
     *  @param $password unhashed user password to test against db
     *  @return true on successful login, false on failure
     */
    function user_login( $username, $password )
    {
        // basic test for good entry
        if( !$username || !$password ){
            return false;
        }
        
        // TODO
        // add test for valid username and password
        // don't forget to hash password
        
        $this->user_id = $this->id_from_name( $username );
        
        if( $this->user_id < 0 ){
            return false;
        }
        
        $this->do_login();
        
        return true;
    }
    
    /** Attempts to login using the LDAP server. Config for the LDAP
     *  connection should be provided in an array as the first argument
     *  I don't have a damn clue what's going on with this to be honest
     *  but I think it will be cool.
     */
    function ldap_login( $auth )
    {
        
        // TODO
        // accept some sort of POST request from the LDAP server and
        // get whatever authorization and id is required to match the
        // user to our database
        
        $this->do_login();
    }
    
    /** Sets the appropriate session variables to save the user as
     *  a logged in user as well as this classes users.
     */
    private function do_login()
    {
        // set logged in to true
        $this->is_logged_in = true;
        
        // load the user
        $this->load_user( $this->user_id );
        //$this->user_name = $this->name_from_id($this->user_id);
        
        // set session vars
        if( !isset($_SESSION['logged_in']) ){
            $_SESSION["logged_in"] = $this->user_id;
        }
    }
    
    /** Ends the current session and destroys the session variable
     *  for this user.
     */
    function do_logout( $user_id )
    {
        $this->is_logged_in = false;
        $this->user_level = -1;
        $this->user_id = -1;
        $_SESSION['logged_in'] = -1;
        unset($_SESSION['logged_in']);
    }
    
    
    /**
     */
    function load_user( $userid ){
        
        
        // make sure this is a valid string
        if( !is_int($userid) ){
            return null;
        }
        
        // start as null
        $username = null;
        
        
        /* =================================
         * Query databse for the user handle
         */
        
        // start a new sql statement
        $stmt = $this->db->select('users');
        // select by username
        $stmt->where('id','i',$userid);
        // limit to 1
        $stmt->limit(1);
        // specify the fild to get from the database
        $stmt->field('handle');
        $stmt->field('userlevel');
                
        // get result of that statement
        $result = $this->db->statement_result($stmt);
        
        // ensure a record was found
        if( count($result) == 0 ){
            return null;
        }
        
        // get the first row from our results
        $row = $result[0];
        
        // get the name
        $this->user_name = $row['handle'];
        
        // get user level
        $this->user_level = (int) $row['userlevel'];
    }
    
    
    /* Getters */
    
    /** Gets the current user level of this user
     *  @return integer user level
     */
    function get_user_level()
    {
        return $this->user_level;
    }
    
    /** Gets the id of this user
     *  @return current users id
     */
    function get_user_id()
    {
        return $this->user_id;
    }
    
    /** Gets the user handle defined by this user at login.
     *  It should be the same as their netid for isu studnts
     *  @return String of the current user's username.
     */
    function get_user_name()
    {
        return $this->user_name;
    }
    
    /** Gets the entire associative array of user settings for
     *  this user.
     *  @return an associative array of all the user settings in the 
     *          database.
     */
    function get_user_settings()
    {
        // check if need to prepare new query
        if( $this->db->is_prepared('user_settings_stmt') == false ){
            $this->db->prepare('user_settings_stmt',
                "SELECT * from " . $this->db->prefix() . 
                "user_settings WHERE id = ? LIMIT 1"
            );
        }
        
        // create a new selection statement
        $stmt = $this->db->select('user_setttings');
        // select where id= userid
        $stmt->where('id','i',$this->user_id);
        // only grab 1 record
        $stmt->limit(1);
        
        
        // execute
        $result = $this->db->statement_result( $stmt );
        
        // ensure we have a record
        if( count($result) == 0 ){
            return null;
        }
        
        // get the top row
        $row = $result[0];
        
        // free results if we need to
        if( method_exists($result,'close') ){
            $result->close();
        }
        
        // return the top row of settings.
        return $row;
    }

    /**
     * Prints the desired string, centralized in red
     *
     *  @param $args desired string to be printed
     */
      function printinRed ($args){
            echo '<p style="color: red; text-align: center"> '.$args. '</p>';
    }


   /** Resizes and saves an image to the upload directory.
     *
     *  @param $args prefix to be appended to the image name.
     *  @return image path relative to the upload directory.
     */
    function uploadImage($args=""){
        $allowedExts = array("gif", "jpeg", "jpg", "png"); //allowed extensions for the uploaded file
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp);
        $target = "upload/";
        $resizedFile = "";

        $this->printinRed ( "Return Code: " . $_FILES["file"]["error"] . "<br>" );

        try {

            if ((($_FILES["file"]["type"] == "image/gif") || 
                ($_FILES["file"]["type"] == "image/jpeg") || 
                ($_FILES["file"]["type"] == "image/jpg") || 
                ($_FILES["file"]["type"] == "image/pjpeg") || 
                ($_FILES["file"]["type"] == "image/x-png") || 
                ($_FILES["file"]["type"] == "image/png")) && 
                ($_FILES["file"]["size"] < 2E06) && 
                in_array($extension, $allowedExts)) {

                    if ($_FILES["file"]["error"] > 0) {
                        $this->printinRed ( "Return Code: " . $_FILES["file"]["error"] . "<br>" );
                    } else {
                        //$this->printinRed ( "Upload: " . $_FILES["file"]["name"] . "<br>" );
                        //$this->printinRed ( "Type: " . $_FILES["file"]["type"] . "<br>" );
                        //$this->printinRed ( "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>" );
                        //$this->printinRed ( "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>" ); 

                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target.$_FILES["file"]["name"])) {
                            $resizedFile = $target.$args.'_'.str_replace(' ','_',$_FILES["file"]["name"]);
                            $this->smart_resize_image($target.$_FILES["file"]["name"], null, 
                                self::IMAGE_RESIZE_WIDTH, self::IMAGE_RESIZE_HEIGHT, 1, $resizedFile, true, false, 100 );
                            //$this->printinRed ("Stored in: " . $target . $resizedFile );
                            //$this->printinRed ('Image Saved Successfully');	
                        } else {
                            switch ($_FILES['upfile']['error']) {
                            case UPLOAD_ERR_OK:
                                break;
                            case UPLOAD_ERR_NO_FILE:
                                throw new RuntimeException('No file sent.');
                            case UPLOAD_ERR_INI_SIZE:
                            case UPLOAD_ERR_FORM_SIZE:
                                throw new RuntimeException('Exceeded filesize limit.');
                            default:
                                throw new RuntimeException('Unknown errors.');
                            }
                        } 
                        return $resizedFile;
                    }
                }
        } catch (RuntimeException $e) {
            var_dump($_FILES['upfile']['error']);
            echo $e->getMessage();
        }
        return null;
    }


    /** Gets the user's profile image from the database
     *  @param $args not used
     */
    function getProfileImage($args){
        // new select statement
        $stmt = $this->db->select('profiles');
        $user_id = $this->get_user_id();	     

        // set the where for category name
        $stmt->where('userid', 'i', $args ); 

        // get category return results
        $cat_result = $this->db->statement_result( $stmt );

        if ($cat_result != null){
            // get the user's image path if the statement was found 
            $path = $cat_result[0]['image'];
        }
        return $path; //returns the image path
    }
    

   /** Gets the image of the given item id from the database
     *  @param $args not used
     */
   function getItemImage($args){
		// new select statement
		$stmt = $this->db->select('listings');
	
		// set the where for category name
		$stmt->where('id', 'i', $args ); 
		        
		// get category return results
		$cat_result = $this->db->statement_result( $stmt );
	
		if ($cat_result != null){
	
		// get the user's password if the statement was found 
		$path = $cat_result[0]['image_paths'];
		}
		return $path; //returns the image path
	}
    
       /**
     * Easy image resize function -> acquired from https://github.com/Nimrod007/PHP_image_resize/blob/master/smart_resize_image.function.php
     *
     * @param $file - file name to resize
     * @param $string - The image data, as a string
     * @param $width - new image width
     * @param $height - new image height
     * @param $proportional - keep image proportional, default is no
     * @param $output - name of the new file (include path if needed)
     * @param $delete_original - if true the original image will be deleted
     * @param $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
     * @param $quality - enter 1-100 (100 is best quality) default is 100
     * @return boolean|resource
     */
    function smart_resize_image($file,
        $string = null,
        $width = 0,
        $height = 0,
        $proportional = false,
        $output = 'file',
        $delete_original = true,
        $use_linux_commands = false,
        $quality = 100
    ) {

        if ( $height <= 0 && $width <= 0 ) return false;
        if ( $file === null && $string === null ) return false;

        # Setting defaults and meta
        $info = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
        $image = '';
        $final_width = 0;
        $final_height = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;

        # Calculating proportionality
        if ( $proportional == 1 ) {
            if ($width == 0) $factor = $height/$height_old;
            elseif ($height == 0) $factor = $width/$width_old;
            else $factor = min( $width / $width_old, $height / $height_old );

            $final_width = round( $width_old * $factor );
            $final_height = round( $height_old * $factor );
        }
        else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            // crop image to preserve aspect ratio
            if ( $proportional > 0 ) {
               $cropWidth = ($width_old - $width * $x) / 2;
               $cropHeight = ($height_old - $height * $x) / 2;
            }
        }

        # Loading image to memory according to type
        switch ( $info[2] ) {
        case IMAGETYPE_JPEG: $file !== null ? $image = imagecreatefromjpeg($file) 
            : $image = imagecreatefromstring($string); break;
        case IMAGETYPE_GIF: $file !== null ? $image = imagecreatefromgif($file) 
            : $image = imagecreatefromstring($string); break;
        case IMAGETYPE_PNG: $file !== null ? $image = imagecreatefrompng($file) 
            : $image = imagecreatefromstring($string); break;
        default: return false;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);

            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color = imagecolorsforindex($image, $transparency);
                $transparency = imagecolorallocate($image_resized, $transparent_color['red'], 
                    $transparent_color['green'], 
                    $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            }
            elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, 
            $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


        # Taking care of original, if needed
        if ( $delete_original ) {
            if ( $use_linux_commands ) exec('rm '.$file);
            else @unlink($file);
        }

        # Preparing a method of providing result
        switch ( strtolower($output) ) {
        case 'browser':
            $mime = image_type_to_mime_type($info[2]);
            header("Content-type: $mime");
            $output = NULL;
            break;
        case 'file':
            $output = $file;
            break;
        case 'return':
            return $image_resized;
            break;
        default:
            break;
        }

        # Writing image according to type to the output destination and image quality
        switch ( $info[2] ) {
        case IMAGETYPE_GIF: imagegif($image_resized, $output); break;
        case IMAGETYPE_JPEG: imagejpeg($image_resized, $output, $quality); break;
        case IMAGETYPE_PNG:
            $quality = 9 - (int)((0.9*$quality)/10.0);
            imagepng($image_resized, $output, $quality);
            break;
        default: return false;
        }

        return true;
    }
    
    /** Gets wether or not this user is currently logged in
     *  @return true if logged in 
     */
    function is_logged_in()
    {
        return $this->is_logged_in;
    }
    
    /** Detirmines if the current logged in user is the user specified
     *  by the given id.
     *  @param $user_id The id of the user to test for logged in
     *  @return true if user matches the logged in user, false otherwise
     */
    function is_user( $user_id )
    {
        // see if this user matches the logged in user
        if( $this->is_logged_in && $this->user_id === $user_id ) {
            return true;
        }
        
        // othrwise, nope
        return false;
    }
    
    /** Gets the user id of a user with a provided username.
     *  @param $username string username of user to look up
     *  @return id of user with provided username on success, -1 on fail
     */
    function id_from_name( $username ){
        
        $user_id = -1;
        
        // make sure this is a valid string
        if( $username == null ){
            return -1;
        }
        
        // if its already a number, convert and return
        if( is_numeric($username) ){
            // use coersion to quickly transform
            $user_id = (int) $username;
                        
            // ensure integer
            if( !is_int($user_id) ){
                return -1;
            }
            return $user_id;
        }
        
        
        /* =============================
         * Query databse for the user id
         */
        
        // start a new sql statement
        $stmt = $this->db->select('users');
        // select by username
        $stmt->where('handle','s',$username);
        // limit to 1
        $stmt->limit(1);
        // select only id
        $stmt->fields(array('id'));
                
        // get result of that statement
        $result = $this->db->statement_result($stmt);
        
        // ensure a record was found
        if( count($result) == 0 ){
            $result->close();
            return -1;
        }
       
        
        // get the first row from our results
        $row = $result[0];
        
        // get the id
        $user_id = (int)$row['id'];
        
        
        return $user_id;
    }
    
    
    /** Gets the username handle of a user with a provided id.
     *  @param $userid interger id of user to look up
     *  @return string username of user with user id on success, 
     *          -1 on fail
     */
    function name_from_id( $userid ){
        
        
        // make sure this is a valid string
        if( !is_int($userid) ){
            return null;
        }
        
        // start as null
        $username = null;
        
        
        /* =================================
         * Query databse for the user handle
         */
        
        // start a new sql statement
        $stmt = $this->db->select('users');
        // select by username
        $stmt->where('id','i',$userid);
        // limit to 1
        $stmt->limit(1);
        // specify the fild to get from the database
        $stmt->field('handle');
                
        // get result of that statement
        $result = $this->db->statement_result($stmt);
        
        // ensure a record was found
        if( count($result) == 0 ){
            return null;
        }
        
        // get the first row from our results
        $row = $result[0];
        
        // get the id
        $username = $row['handle'];
        
        // return the name
        return $username;
    }

    /** Gets the email a user with a provided id.
     *  @param $userid interger id of user to look up
     *  @return string username of user with user id on success, 
     *          -1 on fail
     */
    function email_from_id( $userid ){
        
        
        // make sure this is a valid string
        if( !is_int($userid) ){
            return null;
        }
        
        // start as null
        $email = null;
        
        
        /* =================================
         * Query databse for the user handle
         */
        
        // start a new sql statement
        $stmt = $this->db->select('users');
        // select by username
        $stmt->where('id','i',$userid);
        // limit to 1
        $stmt->limit(1);
        // specify the fild to get from the database
        $stmt->field('email');
                
        // get result of that statement
        $result = $this->db->statement_result($stmt);
        
        // ensure a record was found
        if( count($result) == 0 ){
            $result->close();
            return null;
        }
        
        // get the first row from our results
        $row = $result[0];
        
        // get the id
        $email = $row['email'];
        
        // return the name
        return $email;
    }
}

?>
