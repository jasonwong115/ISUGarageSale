package info.androidhive.slidingmenu.model;

public class UserLoginInfo {
	private int id;
	private String uid;

	public UserLoginInfo(int id, String uid) {
		this.id = id;
		this.uid = uid;
	}

	public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public String getUid() {
		return uid;
	}

	public void setUid(String uid) {
		this.uid = uid;
	}

}
