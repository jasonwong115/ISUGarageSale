package main.development;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.v4.app.DialogFragment;

/**
 * Dialog showed when form submission failed
 * 
 * @author jasonwong
 * 
 */
public class FailureDialogFragment extends DialogFragment {

	// Use this instance of the interface to deliver action events
	OnFragmentClickListenerFail mListener;

	// Default message to display
	String failureMessage = "Error occurred!";

	/**
	 * If any buttons on the dialog are pressed, infrom the main activity
	 * 
	 * @author jasonwong
	 * 
	 */
	public interface OnFragmentClickListenerFail {
		public void onFragmentClick(int action, Object object);
	}

	/**
	 * Create a new instance of SubmittedDialogFragment, providing the message
	 * to be shown as an argument.
	 */
	public static FailureDialogFragment newInstance(String message) {

		FailureDialogFragment f = new FailureDialogFragment();

		// Supply message input as an argument.
		Bundle args = new Bundle();
		args.putString("message", message);
		f.setArguments(args);

		return f;
	}

	// Override the Fragment.onAttach() method to instantiate the
	// FailureDialogFragment
	@Override
	public void onAttach(Activity activity) {
		super.onAttach(activity);
		try {
			mListener = (OnFragmentClickListenerFail) activity;
		} catch (ClassCastException e) {
			throw new ClassCastException(activity.toString()
					+ " must implement listeners!");
		}
	}

	@Override
	public Dialog onCreateDialog(Bundle savedInstanceState) {
		
		//Get the message to display, otherwise use default
		failureMessage = getArguments().getString("message");
		if (getArguments().containsKey("message")) {
			failureMessage = getArguments().getString("message");
		} else {
			failureMessage = "Error occured!";
		}
		// Build the dialog and set up the button click handlers
		return new AlertDialog.Builder(getActivity())
				.setMessage(failureMessage)
				.setPositiveButton("Home",
						new DialogInterface.OnClickListener() {
							@Override
							public void onClick(DialogInterface dialog, int id) {
								mListener.onFragmentClick(1, null);
							}
						})
				.setNegativeButton("Try Again",
						new DialogInterface.OnClickListener() {
							@Override
							public void onClick(DialogInterface dialog, int id) {
								mListener.onFragmentClick(2, null);
							}
						}).create();
	}
}
