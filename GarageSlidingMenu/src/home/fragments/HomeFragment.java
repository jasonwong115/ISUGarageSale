package home.fragments;

import home.fragments.ListingFragment.OnProductSelectedListener;
import info.androidhive.slidingmenu.R;
import main.development.MainActivity;
import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;

/**
 * Fragment for home As of now it contains buttons that create searches based on
 * item categories
 * 
 * @author jasonwong
 * 
 */
public class HomeFragment extends Fragment {

	private OnAllSelectedListener listener;

	/**
	 * On the click of a category button, notify the main activity
	 * 
	 * @author jasonwong
	 * 
	 */
	public interface OnAllSelectedListener {
		public void onAllSelected();
	}

	/**
	 * Empty constructor does nothing
	 */
	public HomeFragment() {
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {

		View rootView = inflater.inflate(R.layout.fragment_home, container,
				false);
		
		//All button *************************************************
		Button btnAll = (Button) rootView.findViewById(R.id.buttonAll);
		btnAll.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View view) {
				//Notify main activity of button click
				listener.onAllSelected();

			} // End of onClick
		}); // End of OnClickListener

		//General button *************************************************
		Button btnGeneral = (Button) rootView.findViewById(R.id.buttonGeneral);
		// Wait for user to submit form
		btnGeneral.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View view) {
				Intent i = new Intent(getActivity(), MainActivity.class);
				i.putExtra("searchCategory", "1");
				i.putExtra("auto", "1");
				startActivity(i);

			} // End of onClick
		}); // End of OnClickListener

		//Books button *************************************************
		Button btnBooks = (Button) rootView.findViewById(R.id.buttonBooks);
		btnBooks.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View view) {
				Intent i = new Intent(getActivity(), MainActivity.class);
				i.putExtra("searchCategory", "2");
				i.putExtra("auto", "2");
				startActivity(i);

			} // End of onClick
		}); // End of OnClickListener

		//Sports button *************************************************
		Button btnSports = (Button) rootView.findViewById(R.id.buttonSports);
		btnSports.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View view) {
				Intent i = new Intent(getActivity(), MainActivity.class);
				i.putExtra("searchCategory", "13");
				i.putExtra("auto", "13");
				startActivity(i);

			} // End of onClick
		}); // End of OnClickListener

		//Art button *************************************************
		Button btnArt = (Button) rootView.findViewById(R.id.buttonArt);
		btnArt.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View view) {
				Intent i = new Intent(getActivity(), MainActivity.class);
				i.putExtra("searchCategory", "9");
				i.putExtra("auto", "9");
				startActivity(i);

			} // End of onClick
		}); // End of OnClickListener

		//Electronics button *************************************************
		Button btnElectronics = (Button) rootView
				.findViewById(R.id.buttonElectronics);
		btnElectronics.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View view) {
				Intent i = new Intent(getActivity(), MainActivity.class);
				i.putExtra("searchCategory", "12");
				i.putExtra("auto", "12");
				startActivity(i);

			} // End of onClick
		}); // End of OnClickListener

		return rootView;
	}

	/*
	 * On the creation of the fragment, setup the listener
	 */
	@TargetApi(Build.VERSION_CODES.HONEYCOMB)
	@Override
	public void onAttach(Activity activity) {
		super.onAttach(activity);
		if (activity instanceof OnProductSelectedListener) {
			listener = (OnAllSelectedListener) activity;
		} else {
			throw new ClassCastException(
					activity.toString()
							+ " must implemenet MyListFragment.OnProductSelectedListener");
		}
	}
}
