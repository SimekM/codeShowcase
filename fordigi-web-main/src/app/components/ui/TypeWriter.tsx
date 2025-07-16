"use client";

import { cn } from "@/app/utils/cn";
import { motion, stagger, useAnimate, useInView } from "motion/react";
import { useEffect, useState } from "react";

export const TypewriterEffect = ({
  words,
  className,
  cursorClassName,
}: {
  words: {
    text: string;
    className?: string;
  }[];
  className?: string;
  cursorClassName?: string;
}) => {
  // split text inside of words into array of characters
  const wordsArray = words.map((word) => {
    return {
      ...word,
      text: word.text.split(""),
    };
  });

  const [scope, animate] = useAnimate();
  const isInView = useInView(scope);
  useEffect(() => {
    if (isInView) {
      animate(
        "span",
        {
          display: "inline-block",
          opacity: 1,
          width: "fit-content",
        },
        {
          duration: 0.3,
          delay: stagger(0.1),
          ease: "easeInOut",
        }
      );
    }
  }, [isInView, animate]);

  const renderWords = () => {
    return (
      <motion.div ref={scope} className="inline">
        {wordsArray.map((word, idx) => {
          return (
            <div key={`word-${idx}`} className="inline-block">
              {word.text.map((char, index) => (
                <motion.span
                  initial={{}}
                  key={`char-${index}`}
                  className={cn(
                    `dark:text-white text-black opacity-0 hidden`,
                    word.className
                  )}
                >
                  {char}
                </motion.span>
              ))}
              &nbsp;
            </div>
          );
        })}
      </motion.div>
    );
  };
  return (
    <div
      className={cn(
        "text-base sm:text-xl md:text-3xl lg:text-5xl font-bold text-center",
        className
      )}
    >
      {renderWords()}
      <motion.span
        initial={{
          opacity: 0,
        }}
        animate={{
          opacity: 1,
        }}
        transition={{
          duration: 0.8,
          repeat: Infinity,
          repeatType: "reverse",
        }}
        className={cn(
          "inline-block rounded-sm w-[4px] h-4 md:h-6 lg:h-10 bg-blue-500",
          cursorClassName
        )}
      ></motion.span>
    </div>
  );
};

export const TypewriterEffectSmooth = ({
  words,
  className,
  cursorClassName,
}: {
  words: {
    text: string;
    className?: string;
  }[];
  className?: string;
  cursorClassName?: string;
}) => {
  const [displayedWords, setDisplayedWords] = useState<typeof words>([]);
  const [hasCompletedFirstCycle, setHasCompletedFirstCycle] = useState(false);

  // Initial typewriter effect
  const [currentWordIndex, setCurrentWordIndex] = useState(0);
  const [currentCharIndex, setCurrentCharIndex] = useState(0);

  useEffect(() => {
    if (hasCompletedFirstCycle) return;
    const timer = setTimeout(() => {
      if (currentWordIndex < words.length) {
        if (currentCharIndex < words[currentWordIndex].text.length) {
          setDisplayedWords(prev => {
            const newWords = [...prev];
            if (!newWords[currentWordIndex]) {
              newWords[currentWordIndex] = {
                text: '',
                className: words[currentWordIndex].className
              };
            }
            newWords[currentWordIndex] = {
              ...newWords[currentWordIndex],
              text: words[currentWordIndex].text.slice(0, currentCharIndex + 1)
            };
            return newWords;
          });
          setCurrentCharIndex(prev => prev + 1);
        } else {
          setCurrentWordIndex(prev => prev + 1);
          setCurrentCharIndex(0);
        }
      } else {
        setHasCompletedFirstCycle(true);
      }
    }, 100);
    return () => clearTimeout(timer);
  }, [currentWordIndex, currentCharIndex, words, hasCompletedFirstCycle]);

  // Smoothly delete and rewrite the last word in a single async cycle
  useEffect(() => {
    if (!hasCompletedFirstCycle) return;
    let cancelled = false;
    const lastWordIndex = words.length - 1;
    const lastWord = words[lastWordIndex];

    async function animateDeleteRewrite() {
      // Wait before starting
      await new Promise(res => setTimeout(res, 2000));
      while (!cancelled) {
        // Delete last word
        for (let i = lastWord.text.length; i >= 0; i--) {
          if (cancelled) return;
          setDisplayedWords(prev => {
            const newWords = [...prev];
            newWords[lastWordIndex] = {
              ...newWords[lastWordIndex],
              text: lastWord.text.slice(0, i)
            };
            return newWords;
          });
          await new Promise(res => setTimeout(res, 180));
        }
        // Pause before rewriting
        await new Promise(res => setTimeout(res, 600));
        // Rewrite last word
        for (let i = 1; i <= lastWord.text.length; i++) {
          if (cancelled) return;
          setDisplayedWords(prev => {
            const newWords = [...prev];
            newWords[lastWordIndex] = {
              ...newWords[lastWordIndex],
              text: lastWord.text.slice(0, i)
            };
            return newWords;
          });
          await new Promise(res => setTimeout(res, 180));
        }
        // Pause before next cycle
        await new Promise(res => setTimeout(res, 2000));
      }
    }
    animateDeleteRewrite();
    return () => { cancelled = true; };
  }, [hasCompletedFirstCycle, words]);

  // split text inside of words into array of characters
  const wordsArray = displayedWords.map((word) => {
    return {
      ...word,
      text: word.text.split("")
    };
  });

  return (
    <div
      className={cn(
        "w-full flex flex-nowrap items-center my-6 min-w-0 max-w-full whitespace-nowrap overflow-hidden",
        className
      )}
      style={{ whiteSpace: "nowrap" }}
    >
      <span className="font-bold min-w-0 max-w-full truncate inline-flex items-center">
        {/* Render all words and the cursor in a single inline row */}
        {wordsArray.map((word, idx) => (
          <span key={`word-${idx}`} className={cn("", word.className)}>
            {word.text.map((char, index) => (
              <span key={`char-${index}`}>{char}</span>
            ))}
            {/* Add space between words except after last word */}
            {idx < wordsArray.length - 1 && <span>&nbsp;</span>}
          </span>
        ))}
        <motion.span
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{
            duration: 0.8,
            repeat: Infinity,
            repeatType: "reverse",
          }}
          className={cn(
            "inline-block align-middle rounded-sm w-1 sm:w-1.5 md:w-2 lg:w-3 ml-1",
            cursorClassName
          )}
          style={{ flexShrink: 0 }}
        ></motion.span>
      </span>
    </div>
  );
};
