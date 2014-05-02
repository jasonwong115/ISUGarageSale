package info.androidhive.slidingmenu.model;

import java.io.IOException;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

public class LoginDatabaseHandler extends SQLiteOpenHelper {
	private static final int DATABASE_VERSION = 1;
	private static final int db_id = 1;

	// Database Name
	private static final String DATABASE_NAME = "LoginDB";

	// Contacts table name
	private static final String TABLE_LOGIN = "LoginTable";

	// Contacts Table Columns names
	private static final String KEY_DB_ID = "db_id";
	private static final String KEY_ID = "id";
	private static final String KEY_UID = "uid";
	private static final String KEY_Email = "email";
	private static final String KEY_Password = "uid";

	public LoginDatabaseHandler(Context context) {
		super(context, DATABASE_NAME, null, DATABASE_VERSION);
	}

	@Override
	public void onCreate(SQLiteDatabase db) {
		String CREATE_LOGIN_TABLE = "CREATE TABLE " + TABLE_LOGIN + "("
				+ KEY_DB_ID + " INTEGER PRIMARY KEY," + KEY_ID + " INTEGER,"
				+ KEY_UID + " TEXT" + ")";
		db.execSQL(CREATE_LOGIN_TABLE);
	}

	@Override
	public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
		db.execSQL("DROP TABLE IF EXISTS " + TABLE_LOGIN);
		
		// Create tables again
		onCreate(db);
	}

	public void addLogin(int id, String uid) {
		SQLiteDatabase db = this.getWritableDatabase();

		ContentValues values = new ContentValues();
		values.put(KEY_DB_ID, db_id);
		values.put(KEY_ID, id);
		values.put(KEY_UID, uid);

		// Inserting Row
		db.insert(TABLE_LOGIN, null, values);
		db.close(); // Closing database connection
	}

	public void removeLogin() {
		SQLiteDatabase db = this.getWritableDatabase();
		db.delete(TABLE_LOGIN, KEY_DB_ID + " = ?",
				new String[] { String.valueOf(db_id) });
		db.close();
	}

	public String getUID() { // input id
		SQLiteDatabase db = this.getReadableDatabase();

		Cursor cursor = db.query(TABLE_LOGIN, new String[] { KEY_UID },
				KEY_DB_ID + "=?", new String[] { String.valueOf(db_id) }, null,
				null, null, null);
		String uid = "";
		if (cursor != null)
			try{
				cursor.moveToFirst();
				uid = cursor.getString(0);
			}finally{
				cursor.close();
			}

		return uid;
	}

	public int getID() {
		SQLiteDatabase db = this.getReadableDatabase();

		Cursor cursor = db.query(TABLE_LOGIN, new String[] { KEY_ID },
				KEY_DB_ID + "=?", new String[] { String.valueOf(db_id) }, null,
				null, null, null);
		int id = -1;
		if (cursor != null)
			try {
				cursor.moveToFirst();
				id = Integer.valueOf(cursor.getString(0));
			} finally {
				cursor.close();
			}

		return id;
	}

	public boolean isLoggedIn() {

		SQLiteDatabase db = this.getReadableDatabase();

		Cursor cursor = db.query(TABLE_LOGIN, new String[] { KEY_ID },
				KEY_DB_ID + "=?", new String[] { String.valueOf(db_id) }, null,
				null, null, null);
		int cursorCount = 0;
		try {
			cursorCount = cursor.getCount();
		} finally {
			cursor.close();
		}

		if (cursorCount > 0) {
			return true;
		} else {
			return false;
		}
	}

	public void backupDatabase() throws IOException {
		SQLiteDatabase db = this.getWritableDatabase();

	}

}
