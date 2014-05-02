package home.fragments;

import java.lang.ref.WeakReference;
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

import home.fragments.ContactFragment.InsertContact;
import info.androidhive.slidingmenu.R;
import info.androidhive.slidingmenu.model.LoginDatabaseHandler;
import android.app.ProgressDialog;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.DialogFragment;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TableRow;
import android.widget.TextView;

public class ProductFragment extends Fragment{
	View rootView;
	Listing product;
	ProgressDialog pDialog;
	MainActivity main;
	LoginDatabaseHandler db;
	
	String offerAmountValue;
	String otherOfferValue;
	String offerCommentValue;
	
	private static final String TAG_SUCCESS = "success";
	JSONParser jsonParser = new JSONParser();
	private WeakReference<InsertContact> asyncTaskWeakRef;
	private static String url_make_offer = "webservice/insert_offer.php";
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
		      Bundle savedInstanceState) {
		    
		    rootView = inflater.inflate(R.layout.fragment_product, container, false);
		    
		    main = (MainActivity) getActivity();
	        db = main.getDbHandler();
		    
		    TextView productName = (TextView) rootView.findViewById(R.id.product_product_name);
		    ImageView image = (ImageView) rootView.findViewById(R.id.product_image);
		    TextView productPrice = (TextView) rootView.findViewById(R.id.product_price);
		    TextView productOtherOffer = (TextView) rootView.findViewById(R.id.product_other_offer);
		    TextView productOwner = (TextView) rootView.findViewById(R.id.product_owner);
		    TextView productDescription = (TextView) rootView.findViewById(R.id.product_description);
		    
		    TableRow row7 = (TableRow) rootView.findViewById(R.id.tableRow7_product);
		    TableRow row8 = (TableRow) rootView.findViewById(R.id.tableRow8_product);
		    TableRow row9 = (TableRow) rootView.findViewById(R.id.tableRow9_product);
		    TableRow row10 = (TableRow) rootView.findViewById(R.id.tableRow10_product);
		    TableRow row11 = (TableRow) rootView.findViewById(R.id.tableRow11_product);
		    
		    TextView makeOfferLabel = (TextView) rootView.findViewById(R.id.Make_an_offer_label);
		    TextView offerAmountLabel = (TextView) rootView.findViewById(R.id.offer_label);
		    TextView otherOfferLabel = (TextView) rootView.findViewById(R.id.other_offer_label);
		    TextView offerCommentLabel = (TextView) rootView.findViewById(R.id.offer_comment_label);
		    EditText offerAmount = (EditText) rootView.findViewById(R.id.offer_amount);
			EditText otherOffer = (EditText) rootView.findViewById(R.id.other_offer);
			EditText offerComment = (EditText) rootView.findViewById(R.id.offer_comment);
		    Button offerButton = (Button) rootView.findViewById(R.id.offer_button);
		    
		    if(db.getID() != product.getUserId()){
		    	makeOfferLabel.setVisibility(View.VISIBLE);
		    	offerAmountLabel.setVisibility(View.VISIBLE);
		    	otherOfferLabel.setVisibility(View.VISIBLE);
		    	offerCommentLabel.setVisibility(View.VISIBLE);
		    	offerAmount.setVisibility(View.VISIBLE);
		    	otherOffer.setVisibility(View.VISIBLE);
		    	offerComment.setVisibility(View.VISIBLE);
		    	offerButton.setVisibility(View.VISIBLE);
        		offerButton.setOnClickListener(new OnClickListener() {

    				@Override
    				public void onClick(View view) {
    					// creating new product in background thread
    					EditText offerAmount = (EditText) rootView.findViewById(R.id.offer_amount);
    					EditText otherOffer = (EditText) rootView.findViewById(R.id.other_offer);
    					EditText offerComment = (EditText) rootView.findViewById(R.id.offer_comment);
    					offerAmountValue = offerAmount.getText().toString();
    					otherOfferValue = otherOffer.getText().toString();
    					offerCommentValue = offerComment.getText().toString();
    					new InsertOffer().execute();
    					Log.d("TEST", "the button clicked...");
    					
    					
    				}// End of onClick
    			}); 
        	}else{
        		row7.setVisibility(View.GONE);
        		row8.setVisibility(View.GONE);
        		row9.setVisibility(View.GONE);
        		row10.setVisibility(View.GONE);
        		row11.setVisibility(View.GONE);
        	}
	    
			productName.setText(product.getTitle());

			if(product.getImageBytes() != null){
	 
	        	Bitmap bMap = BitmapFactory.decodeByteArray(product.getImageBytes(), 0, product.getImageBytes().length);
	        	image.setImageBitmap(bMap);

	        }
			productPrice.setText(product.getAskingPrice());
			productOtherOffer.setText(product.getOtherOffer());
			productOwner.setText(product.getUsername());
			productDescription.setText(product.getDescription());
		
		    return rootView;
	}
	
	/**
	 * Dialog to show if listing has been successfully submitted to the database
	 */
	public void showSuccessDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager().beginTransaction();
		DialogFragment dialog = SubmittedDialogFragment.newInstance("Offer successfully submitted");
        // Create an instance of the dialog fragment and show it
        dialog.show(fm, "SubmittedDialogFragment");
    }
	
	/**
	 * Dialog to show if listing has not been successfully submitted to the database
	 */
	public void showFailureDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager().beginTransaction();
		DialogFragment dialog = FailureDialogFragment.newInstance("Error trying to submit offer!");
        // Create an instance of the dialog fragment and show it
        dialog.show(fm, "FailureDialogFragment");
    }
	
	/**
	 * Background Async Task to Create new product
	 * */
	class InsertOffer extends AsyncTask<String, String, Void> {

		/**
		 * Before starting background thread Show Progress Dialog
		 * */
		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			pDialog = new ProgressDialog(getActivity());
			pDialog.setMessage("Submitting info..");
			pDialog.setIndeterminate(false);
			pDialog.setCancelable(true);
			pDialog.show();
		}
		
		/**
		 * Creating product
		 * */
		protected Void doInBackground(String... args) {
			
			// Building Parameters
			List<NameValuePair> params = new ArrayList<NameValuePair>();
			params.add(new BasicNameValuePair("userid", String.valueOf(db.getID())));
			params.add(new BasicNameValuePair("listingid", String.valueOf(product.getId())));
			params.add(new BasicNameValuePair("offer_price", offerAmountValue));
			params.add(new BasicNameValuePair("offer_other", otherOfferValue));
			params.add(new BasicNameValuePair("comment", offerCommentValue));
			params.add(new BasicNameValuePair("uid", String.valueOf(db.getUID())));

			// getting JSON Object
			// Note that create product url accepts POST method
			JSONObject json = jsonParser.makeHttpRequest(url_make_offer,
					"POST", params);
			
			Log.d("Create Response", json.toString());

			// check for success tag
			try {
				int success = json.getInt(TAG_SUCCESS);

				if (success == 1) {
					//Show SubmittedDialogFragment
					showSuccessDialog();
				} else {
					showFailureDialog();
				}
			} catch (JSONException e) {
				e.printStackTrace();
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

	public void loadProduct(FragmentTransaction fm, Listing product) {
		this.product = product;	
	}

	
}
