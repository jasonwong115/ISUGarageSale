<?php
namespace GarageSale;

/** app/helpers/form.php
 *  Provides an interface for easily creating a new Form
 */
class form_helper {
    
    /** object Instance of the GarageSale application */
    private $app = null;
    
    function __construct( $app )
    {
        $this->app = $app;
    }
    
    /** Creates a new instance of the Form object
     *  @param string $action destination of the form submission
     *  @param string $method method for the submission
     *  @param array $attributes attributes to give to the form 
     */
    function create( $action, $method='POST', $attributes=null )
    {
        return new Form( $this->app, $action, $method, $attributes );
    }
    
    /** Creates a new instance of the Form from another defined form
     *  @param string $action destination of the form submission
     *  @param string $method method for the submission
     *  @param array $attributes attributes to give to the form 
     */
    function create_from( 
        $form_name, $action, $method = 'POST', $attributes = null 
    ){
        // get class to create from
        $form_class = $form_name.'_form';
        return new $form_class( 
            $this->app, $action, $method, $attributes 
        );
    }
}

/** app/helpers/form.php
 *  Provides features for creating forms and validating their input
 *  on submission.
 */
class Form {
	
	/** Text area definition */
	const TYPE_TEXTAREA = 0;
	/** Standard input type definition */
	const TYPE_TEXT = 1;
	/** Submit button type input */
	const TYPE_SUBMIT = 2;
	/** Radio button input */
	const TYPE_RADIO = 3;
	/** Checkbox inputs */
	const TYPE_CHECKBOX = 4;
	/** HTML5 Number input */
	const TYPE_NUMBER = 5;
	/** Password input */
	const TYPE_PASSWORD = 6;
	/** HTML5 color input */
	const TYPE_COLOR = 7;
	/** HTML5 date input */
	const TYPE_DATE = 8;
	/** HTML5 datetime input */
	const TYPE_DATETIME = 9;
	/** HTML5 email input */
	const TYPE_EMAIL = 10;
	/** HTML5 month input */
	const TYPE_MONTH = 11;
	/** HTML5 range input */
	const TYPE_RANGE = 12;
	/** HTML5 search input */
	const TYPE_SEARCH = 13;
	/** HTML5 telephone input */
	const TYPE_TEL = 14;
	/** HTML5 time input */
	const TYPE_TIME = 15;
	/** HTML5 url input */
	const TYPE_URL = 16;
	/** HTML5 week input */
	const TYPE_WEEK = 17;
	/** Adds a select combo box to the form */
	const TYPE_SELECT = 18;
	
	/* Constants for datatype validation */
	
	/** Data type for string */
	const DATA_STRING = 0;
	/** Data type for a number */
	const DATA_NUMBER = 1;
	/** Data type for an intenger */
	const DATA_INTEGER = 2;
	/** Data type for dates */
	const DATA_DATE = 3;
	/** Data type for emails */
	const DATA_EMAIL = 4;
	
	/** Types that inputs can possibly be */
	static $TYPE_ARRAY = array(
	    'textarea',
	    'text',
	    'submit',
	    'radio',
	    'checkbox',
	    'number',
	    'password',
	    'color',
	    'date',
	    'datetime',
	    'email',
	    'month',
	    'range',
	    'search',
	    'tel',
	    'time',
	    'url',
	    'week',
	    'select' 
	);
	
	/** array Contains the inputs provided in the form and contains info
	 *  on how to validate the input
	 *  Array input format should be:
	 *  array(
	 *		'name' => string $input_name,
	 *      'title'=> string $title
	 *      'value'=> string $default_value,
	 *      'type' => int $data_type,
	 *      'required' => bool $is_required,
	 *      'attributes' => array $attributes,
	 *      'options' => array $options,
	 *      'datatype'=> string $data_type
	 *  )
	 *  Attributes is an associative array of attributes for the input,
	 *  i.e. 'class'=>'wysiwyg'
	 *  Options is an associative array of attributes for radios and
	 *  checkboxes. They key is the name the value is the value.
	 *  Data type is the type of data it is to be:
	 *    'i' = int, 's' = string, 'f' = float, 'n' = numeric
	 */
	private $inputs = array();
	
	/** Action destination for submitting the form */
	private $action;
	
	/** method for submission */
	private $method;
	
	/** Instance of the GarageSale application */
	private $app;
	
	/** Offset index for which input is next to print */
	private $print_offset = 0;
	
	/** Creates a new instance of the form helper
	 *  @param object $app Instance of the GarageSale application
	 *  @param string $action path to submit the form to
	 *  @param string $method method for the post 
	 */
	function __construct( $app, $action, $method = 'POST' )
	{
	    $this->app = $app;
		$this->action = $this->app->form_path($action);
		$this->method = $method;
	}
	
	/** Print the form opener to the page
	 *  @param array $attributes associative array of attributes to 
	 *  apply to the form. Key is attr name, value is value  
	 */
	function open( $attributes = null )
	{
        $attr_value = '';
	    // check for attributes
	    if( $attributes != null ){
	        
	        // loop and build values
	        foreach( $attributes as $key => $value ){
	            $attr_value .= $key.'="'.$value.'" ';
	        }
	    }
	
	    $action = $this->action;
	    $method = $this->method;
	    
	    echo <<< FORMOPEN
	    
	    <form action="$action" method="$method" $attr_value>
FORMOPEN;
	}
	
	/** prints the form closure to the page */
	function close()
	{
	    echo <<< FORMCLOSE
	    
	    </form>
FORMCLOSE;
	}
	
	
	/** Prints the next input in the list of inputs
	 *  @return true on success or false if there is nothing to print
	 */
	function print_next()
	{
	    // check if anything left to print
	    if( $this->print_offset >= count($this->inputs) ){
	        return false;
	    }
	    
	    // get this input
	    $input = $this->inputs[$this->print_offset];
	    
	    // print this input
	    $this->print_input( $input );
	    
	    // increase offset
	    $this->print_offset++;
	    
	    // success return
	    return true;
	}
	
	function print_input( $input ){
	    
	    // build attributes
        $attr_value = (isset($input['title'])) ? 
            'title="'.$input['title'].'" ' :
            '';
	    // check for attributes
	    if( $input['attributes'] != null ){
	        
	        // loop and build values
	        foreach( $input['attributes'] as $key => $value ){
	            $attr_value .= $key.'="'.$value.'" ';
	        }
	    }
	    
	    // print text area inputs
	    if( $input['type'] == Form::TYPE_TEXTAREA ){
	        // set default value
	        $set_value = ($input['value']==null) ? '' : $input['value'];
	        
	        echo <<< TEXTAREA
	        
	        <textarea name="${input['name']}" $attr_value
	            >$set_value</textarea>
TEXTAREA;


        // print the input
	    } else if( 
	        $input['type'] == Form::TYPE_SELECT ||
	        $input['type'] == Form::TYPE_RADIO  ||
	        $input['type'] == Form::TYPE_CHECKBOX 
	    ) {
	    
	        if( $input['type'] == Form::TYPE_SELECT ){
	        
	            // select requires some opening
	            echo "<select name=\"${input['name']}\" $attr_value>\n";
	        }
	        
	        // loop over options and build
	        foreach( $input['options'] as $option ){
	            
	            
	            // build attributes
                $attr_ops = '';
                
	            // check for attributes
	            if( $option['attributes'] != null ){
	                
	                // loop and build values
	                foreach( $option['attributes'] as $key => $value ){
	                    $attr_ops .= $key.'="'.$value.'" ';
	                }
	            }
	            
	            // echo based on what type this is
	            if( $input['type'] == Form::TYPE_SELECT ){
	                echo <<< SELECTOP
	                
	                <option value="${option['value']}"
	                    $attr_ops>$text</option>
SELECTOP;
	            } else if( $input['type'] == Form::TYPE_RADIO ) {
	                echo <<< RADIOOP
	                
	                <input type="radio" name="${input['name']}" 
	                    $attr_ops value="${option['value']}" /> 
	                    ${option['text']}
                    <br />
RADIOOP;
	            } else if( $input['type'] == Form::TYPE_CHECKBOX ) {
	                echo <<< CHECKOP
	                
	                <input type="checkbox" name="${input['name']}" 
	                    $attr_ops value="${option['value']}" /> 
	                    ${option['text']}
                    <br />
CHECKOP;
	            }
	            
	        }
	        
	    
	        if( $input['type'] == Form::TYPE_SELECT ){
	        
	            // close select
	            echo "</select>\n";
	        }
	        
	    } else {
	    
	        // get default value
	        $set_value = ($input['value'] == null) ? '' : 
	        ' value="'.$input['value'].'" ';
	        
	        // get default type
	        $type = self::$TYPE_ARRAY[$input['type']];
	        
	        echo <<< INPUT
	        
	        <input type="$type" name="${input['name']}" $attr_value
	            $set_value />
INPUT;
	    }
	}
	
	/** Gets an input from the form based on its name 
	 *  @param string $name name of the input field
	 *  @return pointer Reference to input entry for this object
	 */
	function get( $name )
	{
	    foreach( $this->inputs as $input ){
	        // loop over each and see if match
	        if( $input['name'] == $name ){
	            // return a reference
	            return $input;
	        }
	    }
	}
	
	
	/** Validates the inputs from the POST data.
	 *  @param pointer $action_message pointer to the message string to
	 *         save teh validation message to
	 *  @param bool returns true on success, false on failure
	 */
	function validate( &$action_message )
	{
	    // check for request type
	    if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	        $values = $_POST;
	    } else if( $_SERVER['REQUEST_METHOD'] == 'GET' ){
	        $values = $_GET;
	    }
	    
	    // success state
	    $success = true;
	
	    // loop through all expected inputs
	    for( $i=0; $i<count($this->inputs); $i++ ) {
	        
	        $input =$this->inputs[$i]; 
	        
	        // check if is required
	        if( $this->inputs[$i]['required'] && 
	            (!isset($values[$this->inputs[$i]['name']]) ||
	            $values[$this->inputs[$i]['name']] == null) 
            ){
                // determine display value
                $disp_name = (isset($input['title'])) ? 
                    $input['title'] :
                    $input['name'];
                
                // update action message
                $action_message .= "$disp_name is requred.<br />";
                
                // not successful
                $success = false;
            }
	        
	        // check value type
	        if( $this->inputs[$i]['datatype'] == Form::DATA_NUMBER &&
	            isset($values[$this->inputs[$i]['name']]) &&
	            !is_numeric($values[$this->inputs[$i]['name']])
	        ){
                // update action message
                $action_message .= "<br />${input['name']} must be
                    a number.";
                // not successful
                $success = false;
	        }else if(
	            $this->inputs[$i]['datatype'] == Form::DATA_INTEGER &&
	            isset($values[$this->inputs[$i]['name']]) &&
	            ((int)$values[$this->inputs[$i]['name']]) !=
	                $values[$this->inputs[$i]['name']]
            ){
                // update action message
                $action_message .= "<br />${input['name']} must be
                    an integer.";
                // not successful
                $success = false;
	        }
	        
	        // save old data to default values
	        $this->inputs[$i]['value'] = 
	            $values[$this->inputs[$i]['name']];
	    }
	    
	    return $success;
	}
	
	/** Clears form inputs to start fresh
	 *  @return object self reference
	 */
	function reset()
	{
	    $this->inputs = array();
	    return $this;
	}
	
	/** Add an input field to the form 
	 *  @param string $name What to name the input
	 *  @param int $type What type of input this should be (from const)
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @param array $options options for checkbox and radio lists
	 *  @param string $datatype Type of data for validation
	 *  @return object Reference to the same object
	 */
	function input( 
	    $name, $type, $title = null, 
	    $value = null, $required = false, 
	    $attributes = null, $options = null, 
	    $datatype = Form::DATA_STRING
    ){
	    $this->inputs[] = array(
	        'name' => $name,
	        'title'=> $title,
	        'value'=> $value,
	        'type' => $type,
	        'required' => $required,
	        'attributes' => $attributes,
	        'options' => $options,
	        'datatype'=> $datatype
	    );
	    
	    // return self link
	    return $this;
	}
	
	
	/** Adds a textarea input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @param string $datatype Type of data for validation
	 *  @return object reference to this object
	 */
	function textarea( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_TEXTAREA,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	/** Adds a text input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @param string $datatype Type of data for validation
	 *  @return object reference to this object
	 */
	function text( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name,
	        Form::TYPE_TEXT,
	        $title,  
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	/** Adds a text input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function submit( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_SUBMIT,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	
	
	/** Adds a number input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function number( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_NUMBER,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_NUMBER
        );
	}
	
	
	
	/** Adds an integer number input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function number_int( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_NUMBER,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_INTEGER
        );
	}
	
	/** Adds a password input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function password( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_PASSWORD,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	
	
	/** Adds a color input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function color( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_COLOR,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	/** Adds a date input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function date( 
	    $name, $title=null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_DATE,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_DATE
        );
	}
	
	
	/** Adds a datetime input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function datetime( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_DATETIME,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_DATE
        );
	}
	
	/** Adds a month input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function month( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_MONTH,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_DATE
        );
	}
	
	/** Adds a range input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function range( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_RANGE,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	/** Adds a email input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function email( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_EMAIL,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_EMAIL
        );
	}
	
	/** Adds a search input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function search( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_SEARCH,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	/** Adds a tel input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function tel( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_TEL,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	/** Adds a time input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function time( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_TIME,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	/** Adds a url input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function url( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_URL,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	/** Adds a week input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @return object reference to this object
	 */
	function week( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null
	){
        return $this->input(
	        $name, 
	        Form::TYPE_WEEK,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        null,
	        Form::DATA_STRING
        );
	}
	
	/** Adds a radio input to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @param int $datatype type of data for this input
	 *  @return object reference to this object
	 */
	function radio( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null,
	    $options = array(),
	    $datatype = Form::DATA_STRING
	){
        return $this->input(
	        $name, 
	        Form::TYPE_RADIO,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        $options,
	        $datatype
        );
	}
	
	/** Adds a checkbox input list to the form
	 *  @param string $name What to name the input
	 *  @param int $type What type of input this should be (from const)
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @param int $datatype type of data for this input
	 *  @return object reference to this object
	 */
	function checkbox( 
	    $name, $title, $value = null, 
	    $required = false, 
	    $attributes = null,
	    $options = array(),
	    $datatype = Form::DATA_STRING
	){
        return $this->input(
	        $name, 
	        Form::TYPE_CHECKBOX,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        $options,
	        $datatype
        );
	}
	
	/** Adds a select input list to the form
	 *  @param string $name What to name the input
	 *  @param string $title Title to give to input
	 *  @param string $value Default value for the input
	 *  @param bool $required is this input required 
	 *  @param array $attributes Attributes to associate with this input
	 *  @param int $datatype type of data for this input
	 *  @return object reference to this object
	 */
	function select( 
	    $name, $title = null, $value = null, 
	    $required = false, 
	    $attributes = null,
	    $options = array(),
	    $datatype = Form::DATA_STRING
	){
        return $this->input(
	        $name, 
	        Form::TYPE_SELECT,
	        $title, 
	        $value, 
	        $required, 
	        $attributes,
	        $options,
	        $datatype
        );
	}
	
	
	/** Creates a new option entry for radio, checkbox, and select items
	 *  @param mixed $value the value to give this option
	 *  @param string $text What is the display title for this option
	 *  @param array $attr attributes for this option
	 *  @return array option entry usable by the form helper
	 */
	static function option( $value, $text, $attr = null )
	{
	    return array(
	        'value' => $value,
	        'text' => $text,
	        'attributes' => $attr
	    );
	}
}

?>
