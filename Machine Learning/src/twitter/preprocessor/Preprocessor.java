package twitter.preprocessor;

import twitter.Main;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.logging.Logger;

/**
 * @author Kieran Brahney
 * @version 1.0
 * @package twitter.preprocessor
 * @since 11/02/14 14:02
 */
public class Preprocessor {

    private ArrayList<User> users = new ArrayList<User>();

    public ArrayList<User> getUsers() {
        if (users.size() > 1)
            return users;

        try {
            Statement stmt = Main.getDb().getConnection().createStatement();
            ResultSet rs = stmt.executeQuery(
                    "SELECT `user_id`, `screen_name`, `total_followers`, `total_friends`, `labels_consensus`.`verdict` " +
                    "FROM `labels_consensus` " +
                    "INNER JOIN `users` ON `users`.`user_id` = `labels_consensus`.`twitter_id`"
            );

            while (rs.next()) {
                // Create user
                User user = new User(rs.getLong(1), rs.getString(2), rs.getInt(3), rs.getInt(4), rs.getString(5));
                // Get their tweets
                user.setTweets();
                // Store the user
                users.add(user);
            }
        } catch (SQLException e) {
            Logger.getLogger(Logger.GLOBAL_LOGGER_NAME).warning(e.getMessage());
        }

        return users;
    }

}
