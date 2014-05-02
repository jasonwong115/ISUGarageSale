package home.fragments;

import home.fragments.ListingFragment.OnProductSelectedListener;
import info.androidhive.slidingmenu.R;
import info.androidhive.slidingmenu.model.LoginDatabaseHandler;
import info.androidhive.slidingmenu.model.UserLoginInfo;

import java.util.concurrent.ExecutionException;

import main.development.MainActivity;
import android.annotation.TargetApi;
import android.app.Activity;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class LoginFragment extends Fragment implements OnClickListener{
		View rootView;
		LoginDatabaseHandler db;
		MainActivity main;
		private OnLoginSelectedListener listener;
		public interface OnLoginSelectedListener {
		      public void onLoginSelected();
		  }
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
		      Bundle savedInstanceState) {
		    rootView = inflater.inflate(R.layout.fragment_login, container, false);
		    TextView output = (TextView) rootView.findViewById(R.id.login_output);
		    Button b = (Button) rootView.findViewById(R.id.login_button);
	        b.setOnClickListener(this);
	        main = (MainActivity) getActivity();
	        LoginDatabaseHandler db = main.getDbHandler();
	       
	        if(db.isLoggedIn()){
	        	output.setText("Logged In");
	        }else{
	        	output.setText("Logged Out");
	        }
		    return rootView;
		  }
	

		

	@Override
	public void onClick(View v) {
		EditText username = (EditText) rootView.findViewById(R.id.username);
		EditText password = (EditText) rootView.findViewById(R.id.password);
		TextView output = (TextView) rootView.findViewById(R.id.login_output);
		
		LoginTask task = (LoginTask) new LoginTask().execute(username.getText().toString(),password.getText().toString());
		UserLoginInfo info = null;
		try {
			info = task.get();
		} catch (InterruptedException e) {
			e.printStackTrace();
		} catch (ExecutionException e) {
			e.printStackTrace();
		}
		if(info != null){
			
			LoginDatabaseHandler db = main.getDbHandler();
			db.addLogin(info.getId(), info.getUid());
			getFragmentManager().popBackStackImmediate();
			output.setText(db.getUID() + "success!");
			listener.onLoginSelected();
		}else{
			output.setText("Login Failed");
		}
		
	}
	@TargetApi(Build.VERSION_CODES.HONEYCOMB)
	@Override
	    public void onAttach(Activity activity) {
	      super.onAttach(activity);
	      if (activity instanceof OnProductSelectedListener) {
	        listener = (OnLoginSelectedListener) activity;
	      } else {
	        throw new ClassCastException(activity.toString()
	            + " must implemenet MyListFragment.OnProductSelectedListener");
	      }
	    }
}
