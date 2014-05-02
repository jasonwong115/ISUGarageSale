package main.development;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.v4.app.DialogFragment;

/**
 * Dialog showed when a success dialog is needed
 * 
 * @author jasonwong
 * 
 */
public class SubmittedDialogFragment extends DialogFragment {

	// Message to display on the dialog
	String successMessage = "Thanks for submitting";

	// Use this instance of the interface to deliver action events
	OnFragmentClickListener mListener;

	/**
	 * On the click of a button on the dialog, inform the main activity
	 * 
	 * @author jasonwong
	 * 
	 */
	public interface OnFragmentClickListener {
		public void onFragmentClick(int action, Object object);
	}

	/**
	 * Create a new instance of SubmittedDialogFragment, providing the message
	 * to be shown as an argument.
	 */
	public static SubmittedDialogFragment newInstance(String message) {
		SubmittedDialogFragment f = new SubmittedDialogFragment();

		// Supply num input as an argument.
		Bundle args = new Bundle();
		args.putString("message", message);
		f.setArguments(args);

		return f;
	}

	// Override the Fragment.onAttach() method to instantiate the
	// SubmittedDialogListiner
	@Override
	public void onAttach(Activity activity) {
		super.onAttach(activity);
		try {
			mListener = (OnFragmentClickListener) activity;
		} catch (ClassCastException e) {
			throw new ClassCastException(activity.toString()
					+ " must implement listeners!");
		}
	}

	@Override
	public Dialog onCreateDialog(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		// If message passed using newInstance use that, otherwise use default
		successMessage = getArguments().getString("message");
		if (getArguments().containsKey("message")) {
			successMessage = getArguments().getString("message");
		} else {
			successMessage = "Thanks for submitting!";
		}
		// Build the dialog and set up the button click handlers
		return new AlertDialog.Builder(getActivity())
				.setMessage(successMessage)
				.setPositiveButton("Home",
						new DialogInterface.OnClickListener() {
							@Override
							public void onClick(DialogInterface dialog, int id) {
								mListener.onFragmentClick(1, null);
							}
						})
				.setNegativeButton("Stay on Page",
						new DialogInterface.OnClickListener() {
							@Override
							public void onClick(DialogInterface dialog, int id) {
								mListener.onFragmentClick(2, null);
							}
						}).create();
	}
}
