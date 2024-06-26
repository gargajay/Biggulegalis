<?php

return [
    //Configuration Message
    'S3_BUCKET_CREDENTIALS' => 'AWS credentials not found.',
    'S3_BUCKET_URL' => 'AWS url not found.',
    'MAIL_CREDENTIALS' => 'Mail credentials not found.',
    'SMS_CREDENTIALS' => 'SMS credentials not found.',
    'PUSH_NOTIFICATION_CREDENTIALS' => 'Push notification credentials not found.',
    'STRIPE_CREDENTIALS' => 'Stripe credentials not found.',

    //Route Message
    'PAGE_NOT_FOUND' => 'Page Not Found.',
    'TOO_MANY_ATTEMPTS' => 'Too many attempts. Please try again later.',
    'no_permission' => 'You do not have right permissions',

    


    //Auth Message
    'UNAUTHORIZED_ACCESS' => 'Unauthorized: Please log in to access this resource.',
    'LOGIN_FAILED' => 'Invalid password.',
    'LOGIN_SUCCESSFUL' => 'Login Successful.',
    'SOMETHING_WENT_WRONG' => 'Something Went Wrong.',
    'REGISTRATION_SUCCESSFUL' => 'Registration Successful.',
    'ACCOUNT_BLOCKED' => 'Your Account Has Been Suspended.',
    'PROFILE_UPDATED' => 'Your Profile Has Been Successfully Updated.',
    'ADDRESS_UPDATED' => 'Your Address Has Been Successfully Updated.',
    'NOT_FOUND' => 'Information not found.',
    'INVALID_OLD_PASSWORD' => 'The old password does not match.',
    'PASSWORD_RESET_SUCCESS' => 'Your password has been successfully reset.',
    'PASSWORD_CHANGE_SUCCESS' => 'Your password has been successfully changed.',
    'NOTIFICATION_SETTINGS' => 'Notification settings updated successfully.',
    'LOGOUT_USER' => 'Logged out successfully.',
    'PROFILE_FETCHED' => 'Profile fetched successfully.',
    'SETTINGS_FETCHED' => 'Application settings retrieved successfully.',
    'NOTIFICATION_FETCHED' => 'Notifications retrieved successfully.',
    'NOTIFICATION_READ' => 'Notification status updated successfully.',
    'NOTIFICATION_ENABLED' => 'Notifications Enabled',
    'NOTIFICATION_DISABLED' => 'Notifications Disabled',
    'EMAIL_NOTIFICATION_ENABLED' => 'Email Notifications Enabled.',
    'EMAIL_NOTIFICATION_DISABLED' => 'Email Notifications Disabled.',
    'PHONE_NOTIFICATION_ENABLED' => 'Phone Notifications Enabled.',
    'PHONE_NOTIFICATION_DISABLED' => 'Phone Notifications Disabled.',
    'GOALS_FETCHED' => 'Goals fetched successfully.',
    'S3_SECURITY_TOKEN' => 'Security token for S3 bucket has been generated successfully.',
    'DELETE_USER' => 'User deleted successfully.',
    //API Response
    'RECORD_FETCHED' => 'Records fetched successfully.',

    //OTP Message
    'OTP_RESEND' => 'Re-send OTPs easily after :seconds.',
    'OTP_SEND_SUCCESS' => 'One-Time Password (OTP) has been sent.',
    'INVALID_REQUEST' => 'The request was invalid.',
    'OTP_VERIFY_LIMIT' => 'You have exceeded the maximum number of attempts to verify your OTP.',
    'OTP_VERIFY_SUCCESS' => 'OTP verification was successful!',
    'INVALID_OTP' => 'The OTP you entered is invalid.',
    'OTP_EXPIRED' => 'Your OTP (One-Time Password) has expired.',
    'PASSWORD_RESET_SESSION_EXPIRE' => 'Your password reset session has expired.',

    //Forgot email
    'FORGOT_EMAIL_RESEND' => 'Resend reset link easily after :seconds.',
    'FORGOT_EMAIL_SEND_SUCCESS' => 'The reset password link has been sent to this email.',
    'FORGOT_EMAIL_EXPIRED' => 'This link has expired.',


    //Web Admin Message
    'SAVE_SETTINGS' => 'Settings have been saved successfully.',
    'RECORD_SAVED' => 'Record has been saved successfully.',
    'RECORD_ACTIVE' => 'Record has been successfully activated.',
    'RECORD_INACTIVE' => 'Record has been successfully deactivated.',
    'RECORD_DELETE' => 'Record has been deleted successfully.',
    'Inviation_not_accept' => 'Your request to join has not been accepted yet. Please contact the office for further information or inquiries regarding your application.',

    //error page message
    'RESET_LINK_EXPIRED' => [
        'title' => 'Link Expired',
        'message' => 'This reset link has expired. Please request a new link.',
    ],

    'POST_FETCH' => 'Posts fetched successfully.',
    'POST_ADDED' => 'Post added successfully.',
    'POST_UPDATED' => 'Post updated successfully.',
    'POST_DELETED' => 'Post deleted successfully.',
    'IMAGE_DELETED' => 'Post image deleted successfully.',
    'POST_LIKE' => 'Post liked successfully.',
    'POST_DISLIKE' => 'Post disliked successfully.',
    'COMMENT_ADDED' => 'Comment added successfully.',
    'COMMENT_UPDATED' => 'Comment updated successfully.',
    'COMMENT_DELETED' => 'Comment deleted successfully.',
    'POST_COMMENTS_FETCH' => 'Comments fetched successfully.',
    'POST_LIKE_NOTIFY' => ':name liked your post.',
    'POST_COMMENT_NOTIFY' => ':name commented on your post.',

    'LINK_FETCH' => 'Link fetched successfully.',
    'LINK_DETAILS' => 'Link details fetched successfully.',
    'LINK_ADDED' => 'Link added successfully.',
    'LINK_UPDATED' => 'Link updated successfully.',
    'STAFF_DELETED' => 'Staff deleted successfully.',
    'LINK_DELETED' => 'Link deleted successfully.',

    'QUOTE_ADDED' => 'Quote added successfully.',
    'QUOTE_UPDATED' => 'Quote updated successfully.',
    'QUOTE_DELETED' => 'Quote deleted successfully.',


    'ANNOUNCEMENT_ADDED' => 'Announcement added successfully.',
    'ANNOUNCEMENT_UPDATED' => 'Announcement updated successfully.',
    'ANNOUNCEMENT_DELETED' => 'Announcement deleted successfully.',


    
    
    'LINK_DELETED' => 'Link deleted successfully.',
    'MEMBERS_FETCH' => 'Members fetched successfully.',
    'LINK_REMINDER' => 'You have an upcoming event for :name today at :time .',
    'LINK_CREATED' => 'You are invited for event :name at :time .',

    'CHAT_NOT_FOUND' => 'No chat found.',
    'CHAT_FETCHED' => 'Chat list fetched successfully.',
    'CHAT_DETAILS' => 'Chat details fetched successfully.',
    'MESSAGE_SENT' => 'Message sent successfully.',
    'MESSAGE_NOT_SEND' => 'You can send message to this user.',
    'MESSAGE_SENDER' => ':name sent you a message.',
    'USER_UNBLOCKED' => 'User un-blocked successfully.',
    'USER_BLOCKED' => 'User blocked successfully.',
    'USER_BLOCKED_LIST' => 'User blocked list fetched successfully.',

    'GROUP_CREATED' => 'Group created successfully.',
    'GROUP_UPDATED' => 'Group updated successfully.',
    'GROUP_DELETED' => 'Group deleted successfully.',
    'MEMBERS_ADDED' => 'Members added successfully.',
    'MEMBER_REMOVED' => 'Member removed from group.',
    'MEMBER_PROMOTED_TO_ADMIN' => 'Member promoted to admin',
    'MEMBER_DISMISS_FROM_ADMIN' => 'Member dismiss from admin',

    'SUBSCRIPTION_UPDATED' => 'Subscription updated successfully.',
    'SUBSCRIPTION_DETAILS' => 'Subscription details fetched successfully.',
    'SUBSCRIPTION_FAILED' => 'Error occurred, please contact Customer Support.',

    'COUNTRY_DATA_FETCH' => 'Country list fetched successfully.',
    'STATE_DATA_FETCH' => 'State fetched successfully.',
    'DISTRICT_DATA_FETCH' => 'District fetched successfully.',
    'TEHSIL_DATA_FETCH' => 'Tehsil fetched successfully.',
    'GROUP_ROLE_DATA_FETCH' => 'Role list fetched successfully.',
    'MEMBER_FETCH' => 'Member list fetched successfully.',   
    'ASSOCIATION_FETCH' => 'Association details fetched successfully.',  
    
    'Announcement' => 'There is a new announcement for your association. Please check the Announcements tab.',





    
    

    

    'JOINED_MEMBER' => 'New member joined in association.',
    'NEW_VERSION' => 'Update now available on Play Store. Experience the latest features!',
    
    'Invitation' => 'Assocation send invitation to join them',

    
];
