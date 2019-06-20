<?php
echo do_shortcode( '[mycred_my_rank show_logo=1]' );
//echo do_shortcode( '[mycred_users_of_rank]' );
//echo do_shortcode( '[mycred_users_of_all_ranks]' );
echo do_shortcode( '[mycred_list_ranks wrap="ul"]
<li>%rank_logo% %rank% <span class="req">%min% – %max%</span></li>
[/mycred_list_ranks]' );
