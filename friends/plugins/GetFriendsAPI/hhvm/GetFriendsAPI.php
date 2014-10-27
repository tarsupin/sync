<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows another site to retrieve a list of friends of a user.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array(
		"uni_id"		=> $uniID
	,	"page"			=> $page			// Defaults to 1
	,	"return_num"	=> $returnNum		// Defaults to 50
	);
	
	// Connect to this API from UniFaction
	$friends = Connect::to("sync_friends", "GetFriendsAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

$friends = array(
	array("uni_id" => $friendID, "handle" => $friendHandle, "display_name" => $friendName)
,	array("uni_id" => $friendID, "handle" => $friendHandle, "display_name" => $friendName)
);

*/

class GetFriendsAPI extends API {
	
	
/****** API Variables ******/
	public bool $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public string $encryptType = "fast";		// <str> The encryption algorithm to use for response, or "" for no encryption.
	public array <int, str> $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public int $microCredits = 50;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public int $minClearance = 8;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	): array <int, array<str, mixed>>					// RETURNS <int:[str:mixed]> the response depends on the type of command being requested.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!isset($this->data['uni_id']))
		{
			return false;
		}
		
		// Prepare Values
		$page = isset($this->data['page']) ? (int) $this->data['page'] : 1;
		$returnNum = isset($this->data['return_num']) ? (int) $this->data['return_num'] : 50;
		
		// Get the list of notifications for the user
		return AppFriends::getFullFriendList($this->data['uni_id'], $page, $returnNum);
	}
	
	
}