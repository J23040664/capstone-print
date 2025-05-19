import random

# List of valid shapes with unique initials
SHAPES = ['circle', 'square', 'triangle', 'oval', 'heart', 'rectangle', 'pentagon']

# Maps initials to shape names
INITIAL_MAP = {shape[0]: shape for shape in SHAPES}


def main():
    print("Welcome to Master Mind – Shape Edition!")
    while True:
        difficulty, max_attempts = choose_difficulty()
        secret_code = generate_secret_code()
        attempts = 0
        won = False

        while True:
            print(f"\nAttempt {attempts + 1}")
            guess = get_user_guess()
            if guess is None:
                continue  # Invalid input, do not count attempt

            correct_place, correct_shape = evaluate_guess(secret_code, guess)
            attempts += 1

            print(f"Correct position: {correct_place} | Wrong position: {correct_shape}")

            if correct_place == 4:
                print("You cracked the code!")
                won = True
                break
            elif difficulty == 'hard' and attempts >= max_attempts:
                print("You've reached the maximum number of attempts.")
                break

        print_game_summary(secret_code, guess, attempts, won)

        replay = input("\nDo you want to play again? (yes/no): ").strip().lower()
        if replay != 'yes':
            print("Thanks for playing Master Mind! Goodbye!")
            break


def choose_difficulty():
    while True:
        level = input("Choose difficulty (easy/hard): ").strip().lower()
        if level == 'easy':
            return 'easy', float('inf')
        elif level == 'hard':
            return 'hard', 10
        else:
            print("Invalid input. Please type 'easy' or 'hard'.")


def generate_secret_code():
    return [random.choice(SHAPES) for _ in range(4)]


def get_user_guess():
    user_input = input("Enter 4 shapes (full name or initial, separated by spaces): ").strip().lower().split()
    if len(user_input) != 4:
        print("Invalid input: Please enter exactly 4 shapes.")
        return None

    guess = []
    for item in user_input:
        if item in SHAPES:
            guess.append(item)
        elif item in INITIAL_MAP:
            guess.append(INITIAL_MAP[item])
        else:
            print(f"Invalid shape: '{item}'. Please use valid shapes or initials.")
            return None

    return guess


def evaluate_guess(secret, guess):
    secret_copy = secret[:]
    guess_copy = guess[:]
    correct_place = 0
    correct_shape = 0

    # First, count shapes in correct positions
    for i in range(4):
        if guess[i] == secret[i]:
            correct_place += 1
            secret_copy[i] = None
            guess_copy[i] = None

    # Then count shapes in the wrong positions
    for i in range(4):
        if guess_copy[i] and guess_copy[i] in secret_copy:
            correct_shape += 1
            index = secret_copy.index(guess_copy[i])
            secret_copy[index] = None

    return correct_place, correct_shape


def print_game_summary(secret_code, final_guess, attempts, won):
    print("\n" + "-" * 55)
    print("                 Game Result Summary")
    print("-" * 55)
    print(f"Secret Code     : {' '.join(secret_code)}")
    print(f"Final Guess     : {' '.join(final_guess)}")
    print(f"Total Attempts  : {attempts}")
    print(f"Game Status     : {'WON' if won else 'LOST'}")
    print("-" * 55)


# Run the game
if __name__ == "__main__":
    main()
