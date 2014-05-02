package home.fragments;


import info.androidhive.slidingmenu.R;

import java.util.ArrayList;
import java.util.List;

import main.development.FailureDialogFragment;
import main.development.JSONParser;
import main.development.LDAPParser;
import main.development.SubmittedDialogFragment;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.ProgressDialog;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.DialogFragment;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
/**
 * 
 * @author jasonwong
 * Fragment allows new users to register for a new account with ISU Garage Sale
 */
public class RegisterFragment extends Fragment{
	
	//Keep track of elements in view for the async task
	EditText inputRetypePassword;
	EditText inputEmail;
	EditText inputPassword;
	TextView passwordStrength;
	ProgressDialog pDialog;
	View rootView;
	
	//LDAP Parser used by async task as well
	LDAPParser ldap = null;
	
	//URL for the webservice
	private static String url_create_product = "webservice/register.php";
	private static final String TAG_SUCCESS = "success";
	
	//Constructor, does nothing
	public RegisterFragment(){}
	

	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		//Load proper inputs
        rootView = inflater.inflate(R.layout.fragment_register, container, false);
        Button btnSubmitContact = (Button) rootView.findViewById(R.id.submitRegistration);
		inputRetypePassword = (EditText) rootView.findViewById(R.id.inputRetypePassword);
		inputEmail = (EditText) rootView.findViewById(R.id.inputUserEmail);
		inputPassword = (EditText) rootView.findViewById(R.id.inputChosenPassword);
		inputPassword.addTextChangedListener(watch);
		passwordStrength = (TextView) rootView.findViewById(R.id.passwordStrength);
		
		//Wait for user to submit form and check for valid input
        btnSubmitContact.setOnClickListener(new OnClickListener() {

        	@Override
			public void onClick(View view) {
        		String retypePassword = inputRetypePassword.getText().toString();
    			String email = inputEmail.getText().toString();
    			String password = inputPassword.getText().toString();
    			boolean passwordFormat = false;
				try{
					//Only check for valid net id if something was entered
					ldap = null;
					if(!email.trim().equalsIgnoreCase("")){
						ldap = new LDAPParser(email);
					}
					if (password.matches(".*[0-9].*") && password.matches(".*[a-zA-Z].*")) { 
						passwordFormat = true; 
					} 
					//Something has been incorrectly entered
					if (ldap==null || !ldap.userExists()|| retypePassword.trim().equalsIgnoreCase("") 
							|| email.trim().equalsIgnoreCase("") || password.trim().equalsIgnoreCase("")
							|| !retypePassword.equals(password)
							|| password.length() < 5 || !passwordFormat)
					{
						//Unset all errors first
						inputRetypePassword.setError(null);
						inputPassword.setError(null);
						inputEmail.setError(null);
						
						if(retypePassword.trim().equalsIgnoreCase("")){
							inputRetypePassword.setError("Enter a password again!");
						} if(password.trim().equalsIgnoreCase("")){
							inputPassword.setError("Enter a password!");
						} if(email.trim().equalsIgnoreCase("") || !ldap.userExists()){
							//Overwrites error from LDAP if email never given in first place
							inputEmail.setError("Enter a valid ISU Net-ID!");
						} if(!retypePassword.equals(password)){
							inputRetypePassword.setError("Passwords did not match!");
						} if(password.length() < 5 || !passwordFormat){
							inputRetypePassword.setError("Must be: 6 characters long, and contain at least 1 number and 1 letter");
						} // End of if
					//Input was valid input, so start registration process	
					}else{
			        	new SubmitRegistration().execute();
			        } // End of else
			    }catch (Exception e)
			    {
			    	showFailureDialog();
			    } // End of catch
			}// End of onClick
		}); // End of OnClickListener
        return rootView;
    }
	
	/*
	 * Dialog to show if listing has been successfully submitted to the database
	 */
	public void showSuccessDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager().beginTransaction();
		DialogFragment dialog = SubmittedDialogFragment.newInstance("Check your ISU email to activate your account!");
        // Create an instance of the dialog fragment and show it
        dialog.show(fm, "SubmittedDialogFragment");
    }
	
	/*
	 * Dialog to show if listing has not been successfully submitted to the database
	 */
	public void showFailureDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager().beginTransaction();
		DialogFragment dialog = FailureDialogFragment.newInstance("Error trying to submit registration!");
        // Create an instance of the dialog fragment and show it
        dialog.show(fm, "FailureDialogFragment");
    }
	
	/**
	 * Background Async Task to Create new product
	 * */
	class SubmitRegistration extends AsyncTask<String, String, String> {

		/*
		 * Before starting background thread Show Progress Dialog
		 */
		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			pDialog = new ProgressDialog(getActivity());
			pDialog.setMessage("Submitting registration..");
			pDialog.setIndeterminate(false);
			pDialog.setCancelable(true);
			pDialog.show();
		}
		
		/*
		 * Creating product
		 */
		protected String doInBackground(String... args) {
			
			//Information inputted by user
			String name = ldap.getName();
			String phone = ldap.getPhone();
			String email = inputEmail.getText().toString();
			String username = email;
			String password = inputPassword.getText().toString();
			String usertype = ldap.getUserClass();
			String major = ldap.getMajor();
			
			// Building Parameters to give to web service
			List<NameValuePair> params = new ArrayList<NameValuePair>();
			params.add(new BasicNameValuePair("name",name));
			params.add(new BasicNameValuePair("phone",phone));
			params.add(new BasicNameValuePair("email",email));
			params.add(new BasicNameValuePair("username",username));
			params.add(new BasicNameValuePair("password",password));
			params.add(new BasicNameValuePair("usertype",usertype));
			params.add(new BasicNameValuePair("major",major));

			// getting JSON Object
			JSONParser jsonParser = new JSONParser();
			JSONObject json = jsonParser.makeHttpRequest(url_create_product,
					"POST", params);
			
			// check log cat for response
			Log.d("Create Response", json.toString());

			// check for success tag
			try {
				int success = json.getInt(TAG_SUCCESS);
				String message = json.getString("message");
				Log.d(message,message);

				if (success == 1) { 
					//Show SubmittedDialogFragment since insertion successful
					showSuccessDialog();
				} else {
					
					showFailureDialog();
					
				}
			} catch (JSONException e) {
				showFailureDialog();
			}

			return null;
		}

		/*
		 * After completing background task Dismiss the progress dialog
		 */
		protected void onPostExecute(String file_url) {
			// dismiss the dialog once done
			pDialog.dismiss();
			
		}

	}
	
	TextWatcher watch = new TextWatcher(){
		  @Override
		  public void afterTextChanged(Editable arg0) {
			  
		  } // End of afterTextChanged
		  @Override
		  public void beforeTextChanged(CharSequence arg0, int arg1, int arg2,
		      int arg3) {
			
		  } // End of beforeTextChanged
		  @Override
		  public void onTextChanged(CharSequence s, int a, int b, int c) {
			  //inputPassword.setText(s,TextView.BufferType.EDITABLE);
			  if(a > 7){
				  passwordStrength.setText("Password strength: strong");
			  }else if(a > 5){
				  passwordStrength.setText("Password strength: medium");
			  }else if (a > 0){
				  passwordStrength.setText("Password strength: weak");
			  }else{
				  passwordStrength.setText("");
			  }
		    
		  } // End of onTextChanged
	}; // End of TextWatcher
}
