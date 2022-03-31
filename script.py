# coding: utf-8


def generate_table_button():
    times = list(range(24))
        
    raw_text = """
    <tr class="bs_tr">
        <td class="bs_td"">
            <button disabled class="booking_button" id="mon_btn_HH">HH:00</button>
        </td>
        <td class="bs_td">
            <button disabled class="booking_button" id="tue_btn_HH">HH:00</button>
        </td>
        <td class="bs_td">
            <button disabled class="booking_button" id="wed_btn_HH">HH:00</button>
        </td>
        <td class="bs_td">
            <button disabled class="booking_button" id="thu_btn_HH">HH:00</button>
        </td>
        <td class="bs_td">
            <button disabled class="booking_button" id="fri_btn_HH">HH:00</button>
        </td>
        <td class="bs_td">                
            <button disabled class="booking_button" id="sat_btn_HH">HH:00</button>
        </td>
        <td class="bs_td">
            <button disabled class="booking_button" id="sun_btn_HH">HH:00</button>
        </td>
    </tr>"""

    for time in times:
        if time <= 9:
            time = "0" + str(time)
        else:
            time = str(time)

        print(raw_text.replace("HH", time))


if __name__ == "__main__":
    generate_table_button()
