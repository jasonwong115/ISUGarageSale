package main.development;

import com.unboundid.ldap.sdk.Entry;
import com.unboundid.ldap.sdk.LDAPConnection;
import com.unboundid.ldap.sdk.LDAPException;
import com.unboundid.ldap.sdk.SearchResult;
import com.unboundid.ldap.sdk.SearchScope;

/**
 * 
 * @author jasonwong Class used to use LDAP to retrieve information from a net
 *         id
 * 
 */
public class LDAPParser {

	// Success of the ldap connection
	boolean success;

	// Information of the user
	SearchResult searchResults = null;

	// Parser used to grab attributes from search results
	Entry entry;

	/**
	 * Constructor created a connection to the LDAP server with given netid
	 * 
	 * @param userid
	 *            is the netid of the user to be checked (ex: jlwong)
	 */
	public LDAPParser(String userid) {
		try {
			// Create LDAP connection to check if email is valid isu email
			LDAPConnection connection = new LDAPConnection();
			connection.connect("ldap.iastate.edu", 389);
			searchResults = connection.search("dc=iastate,dc=edu",
					SearchScope.SUB, "(uid=" + userid + ")");

			success = true;
			entry = searchResults.getSearchEntries().get(0);
		} catch (LDAPException e) {
			success = false;
			searchResults = null;
		}
	}

	/**
	 * @return true if netid is in the LDAP database
	 */
	public boolean userExists() {
		if (searchResults != null) {
			if (searchResults.getEntryCount() > 0) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	 * Using the LDAP attribute, return the value for netid given in constructor
	 * 
	 * @return full name
	 */
	public String getName() {
		return entry.getAttributeValue("cn");
	}

	/**
	 * Using LDAP, return all values
	 * 
	 * @return all attributes of given netid
	 */
	public String getAll() {
		return entry.toString();
	}

	/**
	 * Using the LDAP attribute, return the value for netid given in constructor
	 * 
	 * @return college major
	 */
	public String getMajor() {
		return entry.getAttributeValue("isuPersonMajor");
	}

	/**
	 * Using the LDAP attribute, return the value for netid given in constructor
	 * 
	 * @return the name of the college person is in (engineering, liberal
	 *         sciences, etc)
	 */
	public String getCollege() {
		return entry.getAttributeValue("isuPersonCollege");
	}

	/**
	 * Using the LDAP attribute, return the value for netid given in constructor
	 * 
	 * @return type of class user is (student, faculty, etc)
	 */
	public String getUserClass() {
		return entry.getAttributeValue("userClass");
	}

	/**
	 * Using the LDAP attribute, return the value for netid given in constructor
	 * 
	 * @return postal code of user
	 */
	public String getPostal() {
		return entry.getAttributeValue("postalCode");
	}

	/**
	 * Using the LDAP attribute, return the value for netid given in constructor
	 * 
	 * @return phone number
	 */
	public String getPhone() {
		return entry.getAttributeValue("telephoneNumber");
	}

	/**
	 * Using the LDAP attribute, return the value for netid given in constructor
	 * 
	 * @return whether user is still at Iowa State
	 */
	public String getStatus() {
		return entry.getAttributeValue("isuPersonStatus");
	}

}
