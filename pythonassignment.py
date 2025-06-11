# import random library
import random

# Create a list for stored shape names
shape_lists = ['circle', 'square', 'triangle', 'oval', 'heart', 'rectangle']

# Create a list for stored shape initials
shape_initials = []
for shape in shape_lists:
    shape_initials.append(shape[0])

# Main function is controls the overall game loop, including difficulty selection and replay
def main():
    # print the title
    print("*"*55)
    print("        Welcome to Master Mind – Shape Edition!")
    print("*"*55)
    see_rules = input("Do you want to see how to play? (yes/no): ").strip().lower()
    if see_rules == 'yes':
        show_instructions()
    # print the shape and input example to let user know how to play
    print(f"\nShape List: {shape_lists}\nInput example: (circle square triangle oval) or (c s t o)\n")

    while True:
        # The function to return back the mode and attempt limits
        mode, max_attempts = choose_difficulty()

        # The function to return back the generate random 4 shape
        secret_shape = generate_secret_code()

        # set the current attempts
        cur_attempt = 0

        # set the game status
        game_status = False

        # While loop will break user win or lose the game and ask user replay
        while True:

            # Print current attempts to 1
            print(f"\nAttempt [{cur_attempt + 1}]")

            # The function to get user input for guess the shape
            user_guess = get_user_guess()

            # If user chooses to give up
            if user_guess == "GIVE_UP":
                print("\nYou chose to give up! The secret code was:", " | ".join(secret_shape))
                break
            
            # If the function return to none, 
            # skip the rest of this loop iteration and go back to the start, so won't count the attempt
            if user_guess is None:
                continue

            # The function pass user guess and secret shape, return correct place and correct shape, count the attempt
            correct_place, correct_shape = evaluate_guess(user_guess, secret_shape)
            cur_attempt += 1

            # print the correct place and correct shape
            print(f"Correct position: {correct_place} | Wrong position: {correct_shape}")

            # if correct place equal to 4 mean user guess correct the 4 random shape
            if correct_place == 4:
                print("Congratulations! You cracked the code!")
                # Set the status to true and end the loop
                game_status = True
                break
            # In hard mode user only can attempts 10 times, print the lose message, end the loop
            elif mode == 'hard' and cur_attempt >= max_attempts:
                print("You Lose! You've reached the maximum number of attempts.")
                break

        # The function to pass in all data that needs and print the game result
        print_game_summary(secret_shape, user_guess, mode, cur_attempt, game_status)

        # Ask user want to replay or not
        # get the input as lowercase and remove the space
        replay = input("\nDo you want to play again? (yes/no): ").strip().lower()

        # if no, end the loop
        if replay != 'yes':
            print("Thanks for playing Master Mind! Goodbye!")
            break

# This function is to show steps and rules for this game for first time player.
def show_instructions():
    print("\n" + "="*60)
    print("                HOW TO PLAY - MASTER MIND")
    print("="*60)
    print("1. A secret code of 4 shapes will be randomly generated.")
    print("2. You must guess the correct shapes in the correct order.")
    print("3. Valid shapes: circle, square, triangle, oval, heart, rectangle.")
    print("4. You can enter shapes using their full names or first letters.")
    print("   For example: c s t h (means circle square triangle heart)")
    print("5. After each guess, you’ll receive feedback:")
    print("   ✔ Correct shape in correct place")
    print("   ➜ Correct shape but in the wrong place")
    print("6. EASY mode: Unlimited guesses.")
    print("   HARD mode: Only 10 guesses allowed.")
    print("7. Try to crack the code before you run out of guesses!")
    print("="*60 + "\n")

# The function is generates and returns a list of 4 randomly selected shapes
def generate_secret_code():
    # create a list to stored selected shapes
    secret_shapes = []

    # for loop is for generate 4 random shapes
    for _ in range(4):
        # Random choose the shape from shape_lists and stored into the list
        secret_shapes.append(random.choice(shape_lists))
    # Return the list
    return secret_shapes

# The function is ask the user to select Easy or Hard mode; returns mode and attempt limits
def choose_difficulty():
    while True:
        # get the input as lowercase and remove the spaces
        mode = input("Please select 'easy' or 'hard' mode: ").strip().lower()
        # if easy, return easy and infinite attempt
        if mode == "easy":
            return "easy", float('inf')
        # if hard, return hard and 10 attempt limits
        elif mode == "hard":
            return "hard", 10
        # if other, show the message and let user input again
        else:
            print("Invalid input, please type 'easy' or 'hard'.")

# The function is prompts and validates user input; accepts full shape names or first letters
def get_user_guess():
    # Get the input as lowercase, remove spaces and split it
    user_input = input("Please guess and enter 4 shape names or initials (or enter 999 to give up): ").strip().lower()

    # Check if user wants to give up
    if user_input == '999':
        return "GIVE_UP"

    user_input = user_input.split()

    # if user input not enough 4 shape names, show the message and input again
    if len(user_input) != 4:
        print("Invalid input, please enter 4 shape names or initials")
        # Return none to main function
        return None
    
    # Create list for stored user guess
    guess_list = []

    # for loop to check user input, whether match shape names or initials or not
    for shape in user_input:
        # if user input in shape list, stored the user input into guess list
        if shape in shape_lists:
            guess_list.append(shape)

        # if user input in shape initials, stored the user input into guess list
        elif shape in shape_initials:
            index = shape_initials.index(shape)
            guess_list.append(shape_lists[index])

        # show the message if user input not correct shape names or initials
        else:
            print(f"Invalid shape or initial: '{shape}'")
            return None
        
    return guess_list


# The function is Compares user’s guess to the secret code and returns two values:
# • correct_place: number of shapes in the correct position
# • correct_shape: number of shapes correct but in the wrong position
def evaluate_guess(user_guess, secret_shape):
    # Copy the list to prevent changes the original variable
    user_guess_copy = user_guess.copy()
    secret_shape_copy = secret_shape.copy()

    # Set the count for correct position and shape
    correct_place = 0
    correct_shape = 0

    # For loop to compare user guess and secret shape, 4 is because guess 4 shapes only
    for i in range(4):
        # if user guess variable and secret shape variable is same
        if user_guess_copy[i] == secret_shape_copy[i]:
            # Plus 1 for correct place
            correct_place += 1

            # Set it to none to prevent multiple count
            user_guess_copy[i] = None
            secret_shape_copy[i] = None
    
    for j in range(4):
        # if user guess is not none, and user guess is inside the secret shape list
        if user_guess_copy[j] is not None and user_guess_copy[j] in secret_shape_copy:
            # Plus 1 for correct shape, but wrong position
            correct_shape += 1

            # Set it to none to prevent multiple count
            index = secret_shape_copy.index(user_guess_copy[j])
            secret_shape_copy[index] = None

    # return the correct place and correct shape
    return correct_place, correct_shape

# The function is display the summary of game outcome
# get all the data needs
def print_game_summary(secret_shape, correct_guess, mode, attempts, game_status):
    print("\n" + "*" * 50)
    print(" GAME RESULT ".center(50))
    print("*" * 50)
    print(f"{'Secret Code:':<18} {' | '.join(secret_shape)}")
    print(f"{'Final Guess:':<18} {' | '.join(correct_guess)}")
    print(f"{'Mode:':<18} {mode}")
    print(f"{'Total Attempts:':<18} {attempts}")
    print(f"{'Status:':<18} {'YOU WON!' if game_status else 'YOU LOST!'}")
    print("*" * 50 + "\n")

# Run the main function
main()
