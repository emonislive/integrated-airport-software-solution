# Auto-install dependencies
try:
    import pandas as pd
except ImportError:
    import subprocess, sys
    subprocess.check_call([sys.executable, "-m", "pip", "install", "pandas"])
    import pandas as pd

try:
    import selenium
except ImportError:
    import subprocess, sys
    subprocess.check_call([sys.executable, "-m", "pip", "install", "selenium"])
    import selenium

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
import time

# Load booking data
df = pd.read_csv("Booking_Options_with_Dates.csv")
first_row = df.iloc[0]
FROM_CODE = first_row['From']
TO_CODE = first_row['To']
FLIGHT_DATE = first_row['Flight Date']

# Start browser session
driver = webdriver.Chrome()
driver.maximize_window()

def login():
    print("TC-01: Login as sManager")
    driver.get("http://localhost/integrated-airport-software-solution-main/login.php")
    try:
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "user")))
        driver.find_element(By.NAME, "user").send_keys("sManager")
        driver.find_element(By.NAME, "password").send_keys("123456")
        driver.find_element(By.XPATH, "//button[@type='submit']").click()
        time.sleep(1)
        assert "login" not in driver.current_url.lower()
        print("✅ Login successful")
    except Exception as e:
        print("❌ Login failed:", e)

def test_view_flights():
    print("TC-02: View list of flights")
    driver.get("http://localhost/integrated-airport-software-solution-main/list_flight.php")
    try:
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.TAG_NAME, "table")))
        print("✅ Flight table is visible")
    except Exception as e:
        print("❌ Flight table not found:", e)

def test_view_passengers():
    print("TC-03: View list of passengers")
    driver.get("http://localhost/integrated-airport-software-solution-main/list_passenger.php")
    try:
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.TAG_NAME, "table")))
        print("✅ Passenger table is visible")
    except Exception as e:
        print("❌ Passenger table not found:", e)

def test_booking_search_and_open():
    print("TC-04: Search flights and open book_flight.php with valid flightid")
    driver.get("http://localhost/integrated-airport-software-solution-main/book_flight.php")
    try:
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "from")))
        Select(driver.find_element(By.NAME, "from")).select_by_value(FROM_CODE)
        Select(driver.find_element(By.NAME, "to")).select_by_value(TO_CODE)
        driver.find_element(By.NAME, "date").clear()
        driver.find_element(By.NAME, "date").send_keys(FLIGHT_DATE)
        driver.find_element(By.NAME, "search").click()
        time.sleep(2)

        links = driver.find_elements(By.XPATH, "//a[contains(@href, 'book_flight.php?flightid=')]")
        if links:
            print("✅ Booking link found. Clicking...")
            links[0].click()  # Use click to keep JavaScript & URL context
            print("✅ Navigated to booking page successfully")
        else:
            print("❌ No booking links found")
    except Exception as e:
        print("❌ Error in booking flow:", e)

def test_graph_airplane():
    print("TC-05: View graph of top airplanes")
    driver.get("http://localhost/integrated-airport-software-solution-main/graph_top_airplane.php")
    try:
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, "chartContainer"))
        )
        print("✅ Graph is visible")
    except Exception as e:
        print("❌ Graph check failed:", e)

# Run all tests
login()
test_view_flights()
test_view_passengers()
test_booking_search_and_open()
test_graph_airplane()

input("Press ENTER to close browser...")
driver.quit()
