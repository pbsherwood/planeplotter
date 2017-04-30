import socket
import MySQLdb

conn = MySQLdb.connect (host = "SERVER",
			user = "USERNAME",
			passwd = "PASSWORD",
			db = "DATABASE")
cursor = conn.cursor()

s = socket.socket()
host = socket.gethostname()
port = 31004
s.connect((host, port))

while True:
    current_buffer = s.recv(1024)
    lines = current_buffer.split("\r\n")
    for line in lines[:-1]:
        if line.strip():
            current_line = line.strip("\n")
            curline_split = current_line.split(",")
            if len(curline_split) > 4:
                ICAO = curline_split[4]
                if curline_split[0] == "AIR":
                    dbcall = "insert into data (ICAO) values ('" + ICAO + "');"
                    print dbcall
                    try:
                        cursor.execute (dbcall)
                    except MySQLdb.IntegrityError as e:
                        pass
                if curline_split[0] == "MSG":   # ignore sta lines as they don't have any information of use to me
                    to_update = {}
                    
                    callsign = curline_split[10].strip()
                    altitude = curline_split[11].strip()
                    ground_speed = curline_split[12].strip()
                    heading = curline_split[13].strip()
                    latitude = curline_split[14].strip()
                    longitude = curline_split[15].strip()
                    vertical_rate = curline_split[16].strip()
                    ssr = curline_split[17].strip()
                    alert = curline_split[18].strip()
                    emergency = curline_split[19].strip()
                    spi = curline_split[20].strip()
                    if curline_split[1] != "7":
                        is_on_ground = curline_split[21].strip()


                    if callsign != "":
                        to_update['callsign'] = "'" + callsign + "'"
                    if altitude != "":
                        to_update['altitude'] = altitude
                    if ground_speed != "":
                        to_update['ground_speed'] = ground_speed
                    if heading != "":
                        to_update['heading'] = heading
                    if latitude != "":
                        to_update['latitude'] = latitude
                        to_update['last_location_update_time'] = "unix_timestamp(now())"
                    if longitude != "":
                        to_update['longitude'] = longitude
                        to_update['last_location_update_time'] = "unix_timestamp(now())"
                    if vertical_rate != "":
                        to_update['vertical_rate'] = vertical_rate
                    if ssr != "":
                        to_update['ssr'] = ssr
                    if alert != "":
                        to_update['alert'] = alert
                    if emergency != "":
                        to_update['emergency'] = emergency
                    if spi != "":
                        to_update['spi'] = spi
                    if curline_split[1] != "7":
                        if is_on_ground != "" and is_on_ground != "0":
                            to_update['is_on_ground'] = is_on_ground

                    if len(to_update) > 0:
                        to_update['last_update_time'] = "unix_timestamp(now())"
                        dbcall = ""
                        dbcall += "update data set "
                        dbcall += ", ".join(["" + field +  " = " + to_update[field] for field in to_update])
                        dbcall += " where ICAO = '" + ICAO + "'"

                        print dbcall
                        cursor.execute (dbcall)
            
s.close



##FOR 'STA' CATEGORY
##------------------
##0 = message category
##1 = message type
##2 = FILLER
##3 = FILLER
##4 = ICAO
##5 = FILLER
##6 = date
##7 = time
##8 = date
##9 = time
##10 = UNKNOWN
##
##
##FOR 'AIR' CATEGORY
##------------------
##0 = message category
##1 = message type
##2 = FILLER
##3 = FILLER
##4 = ICAO
##5 = FILLER
##6 = date
##7 = time
##8 = date
##9 = time
##
##
##FOR 'MSG' CATEGORY
##------------------
##0 = message category
##1 = message type
##2 = FILLER
##3 = FILLER
##4 = ICAO
##5 = FILLER
##6 = date
##7 = time
##8 = date
##9 = time
##10 = C/S
##11 = altitude
##12 = ground speed (GS)
##13 = (TT)
##14 = latitude
##15 = longitude
##16 = vertical rate
##17 = SSR (squawk)
##18 = alert (squawk change)
##19 = emergency
##20 = spi (ident)
##21 = IsOnGround
