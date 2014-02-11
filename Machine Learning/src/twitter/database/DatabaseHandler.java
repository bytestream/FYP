package twitter.database;

import twitter.Config;

import java.sql.*;
import java.util.logging.Logger;

/**
 * @author Kieran Brahney
 * @version 1.0
 * @package twitter.database
 * @since 11/02/14 12:59
 */
public class DatabaseHandler {

    private Connection conn = null;

    public DatabaseHandler() {
        try {
            // Register JDBC Driver
            Logger.getLogger(Logger.GLOBAL_LOGGER_NAME)
                    .info("Registering driver: " + Config.getJDBC_DRIVER());
            Class.forName(Config.getJDBC_DRIVER());

            // Open connection
            Logger.getLogger(Logger.GLOBAL_LOGGER_NAME)
                    .info("Connecting to " + Config.getDB_URL() + "@" + Config.getUSER() + ":" + Config.getPASS());
            conn = DriverManager.getConnection(Config.getDB_URL(), Config.getUSER(), Config.getPASS());
        } catch (SQLException e) {
            closeConnection();
            Logger.getLogger(Logger.GLOBAL_LOGGER_NAME).severe(e.getMessage());
            System.exit(-1);
        } catch (ClassNotFoundException e) {
            closeConnection();
            Logger.getLogger(Logger.GLOBAL_LOGGER_NAME).severe(e.getMessage());
            System.exit(-1);
        }
    }

    public void printResult(ResultSet rs) {
        try {
            ResultSetMetaData rsmd = rs.getMetaData();

            // Print the columns
            for (int i = 1; i <= rsmd.getColumnCount(); i++) {
                if (i > 1) System.out.print(",  ");
                System.out.print(rsmd.getColumnName(i));
            }
            System.out.println("");

            // Print rows
            while (rs.next()) {
                // Row indexing starts at 1
                for (int i = 1; i <= rsmd.getColumnCount(); i++) {
                    if (i > 1) System.out.print(",  ");
                    System.out.print(rs.getString(i));
                }
                // New row
                System.out.println("");
            }
        } catch (SQLException e) {
            Logger.getLogger(Logger.GLOBAL_LOGGER_NAME).warning(e.getMessage());
        }
    }

    public Connection getConnection() {
        return conn;
    }

    public void closeConnection() {
        try {
            Logger.getLogger(Logger.GLOBAL_LOGGER_NAME)
                    .info("Disconnecting from DB server.");
            conn.close();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

}
