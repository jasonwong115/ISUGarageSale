package home.fragments;


import info.androidhive.slidingmenu.R;
import info.androidhive.slidingmenu.R.layout;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

/**
 * Fragment for the terms of service for ISU Garage Sale
 * @author jasonwong
 *
 */
public class TermsOfServiceFragment extends Fragment {
	
	/**
	 * Empty constructor does nothing
	 */
	public TermsOfServiceFragment(){}
	
	
	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
 
		//All that is needed is to inflate the fragment
		//All the logic and test is held in the layout
        View rootView = inflater.inflate(R.layout.fragment_terms_of_service, container, false);
        return rootView;
    }
}
