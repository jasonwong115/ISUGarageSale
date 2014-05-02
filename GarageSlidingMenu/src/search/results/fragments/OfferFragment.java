package search.results.fragments;


import info.androidhive.slidingmenu.R;
import info.androidhive.slidingmenu.model.LoginDatabaseHandler;

import java.util.ArrayList;
import java.util.List;

import main.development.FailureDialogFragment;
import main.development.JSONParser;
import main.development.MainActivity;
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
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

/**
 * Detailed view of the offer
 * @author jasonwong
 *
 */
public class OfferFragment extends Fragment{
	ProgressDialog pDialog;
	RadioGroup radioOffer;
	RadioButton radioOfferButton;
	TextView offerSubmitted;
	
	private static String url_create_product = "webservice/update_offer.php";
	private static final String TAG_SUCCESS = "success";
	
	View view;
	Offer offer;
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
		      Bundle savedInstanceState) {
		    
		    view = inflater.inflate(R.layout.fragment_offer, container, false);
		    
		    //Add information from product to view
		    TextView productName = (TextView) view.findViewById(R.id.offer_product);
			productName.setText(offer.getProductName());
			TextView offerComment = (TextView) view.findViewById(R.id.offerComment);
			offerComment.setText(offer.getOfferComment());
			TextView offerPrice = (TextView) view.findViewById(R.id.offerPrice);
			offerPrice.setText("Offer Price: "+offer.getOfferPrice());
			TextView offerStatus = (TextView) view.findViewById(R.id.offerStatus);
			offerStatus.setText("Status: "+offer.getStatusName());
			offerSubmitted = (TextView) view.findViewById(R.id.offerSubmitted);
			radioOffer = (RadioGroup) view.findViewById(R.id.radioOffer);
			Button buttonMarkOffer = (Button) view.findViewById(R.id.buttonMarkOffer);
			MainActivity main = (MainActivity) getActivity();
			LoginDatabaseHandler db = main.getDbHandler();
			offerSubmitted.setVisibility(4);
	        if(db.isLoggedIn()){
	        	int userID;
	        	userID = db.getID();
	        	if(userID == offer.getSeller()){
	        		radioOffer.setVisibility(0);
	        		buttonMarkOffer.setVisibility(0);
	        		buttonMarkOffer.setOnClickListener(new OnClickListener() {

	          			@Override
	          			public void onClick(View view) {
	          				new UpdateOffer().execute();
	          				offerSubmitted.setText("Offer status changed!");
	          				offerSubmitted.setVisibility(0);
	          			} // End of onClick
	          		}); // End of OnClickListener
	        	}else{
	        		radioOffer.setVisibility(4);
	        		buttonMarkOffer.setVisibility(4);
	        	}
	        }else{
	        	radioOffer.setVisibility(4);
	        	buttonMarkOffer.setVisibility(4);
	        }
		    return view;
	}

	/*
	 * Load this fragment with information from offer
	 */
	public void loadOffer(FragmentTransaction fm, Offer offer) {
		this.offer = offer;
	}
	
	/*
	 * Dialog to show if listing has been successfully submitted to the database
	 */
	public void showSuccessDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager().beginTransaction();
		DialogFragment dialog = SubmittedDialogFragment.newInstance("You have marked the offer!");
        // Create an instance of the dialog fragment and show it
        dialog.show(fm, "SubmittedDialogFragment");
    }
	
	/*
	 * Dialog to show if listing has not been successfully submitted to the database
	 */
	public void showFailureDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager().beginTransaction();
		DialogFragment dialog = FailureDialogFragment.newInstance("Error occurred trying to update offer!");
        // Create an instance of the dialog fragment and show it
        dialog.show(fm, "FailureDialogFragment");
    }
	
	/**
	 * Background Async Task to update an offers status
	 * */
	class UpdateOffer extends AsyncTask<String, String, String> {

		/**
		 * Before starting background thread Show Progress Dialog
		 * */
		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			pDialog = new ProgressDialog(getActivity());
			pDialog.setMessage("Marking offer..");
			pDialog.setIndeterminate(false);
			pDialog.setCancelable(true);
			pDialog.show();
		}
		
		/**
		 * Creating product
		 * */
		protected String doInBackground(String... args) {
			
			//Information
			List<NameValuePair> params = new ArrayList<NameValuePair>();
			String listingid = "" + offer.getListingID();
			int reasonID = radioOffer.getCheckedRadioButtonId();
			radioOfferButton = (RadioButton) view.findViewById(reasonID);
			String reason = radioOfferButton.getText().toString();
			int status = 0;
			if(reason.equals("Accept Offer")){
				params.add(new BasicNameValuePair("accept","accept"));
			}else if(reason.equals("Mark Best Offer")){
				params.add(new BasicNameValuePair("best","best"));
			}else{
				params.add(new BasicNameValuePair("decline","decline"));
			}
			int offerID = offer.getOfferID();
			
			// Building Parameters to give to web service
			
			params.add(new BasicNameValuePair("listingid",listingid));
			params.add(new BasicNameValuePair("status",""+status));
			params.add(new BasicNameValuePair("offerid",""+offerID));

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

		/**
		 * After completing background task Dismiss the progress dialog
		 * **/
		protected void onPostExecute(String file_url) {
			// dismiss the dialog once done
			pDialog.dismiss();
			
		}

	}
}
