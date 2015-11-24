on idle
	tell application "System Events"
		tell current location of network preferences
			
			-- check vpn connection status
			set connected_vpn to get name of every service whose (kind is greater than 10 and kind is less than 17) and connected of current configuration is true
			set connect_status to count of connected_vpn
			
			if connect_status is 0 then
				-- get vpn list
				set vpn_list to get name of every service whose (kind is greater than 10 and kind is less than 17)
				
				-- select random one
				set vpn_count to count of vpn_list
				set selected_serial to random number from 1 to vpn_count
				set selected_vpn to item selected_serial of vpn_list
				
				display notification "Connect to " & selected_vpn & " ..."
				
				-- connect select vpn
				set vpn to the service selected_vpn
				if vpn is not null then
					if current configuration of vpn is not connected then
						connect vpn
					end if
				end if
				
			end if
		end tell
		return 30
	end tell
end idle
