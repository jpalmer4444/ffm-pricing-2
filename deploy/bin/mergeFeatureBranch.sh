#! /bin/bash
# @copyright  Copyright (c) 2017 FultonFishMarket
# @author     Jason Palmer <jpalmer@meadedigital.com>

# Merge Feature Branch

mergeFeatureBranch(){
    git checkout master
    git pull origin master
    git merge $1
    git push origin master
}

# Retrieve branch name
echo '======================================================'
branch=$(git branch | sed -n -e 's/^\* \(.*\)/\1/p')

if [ "$branch" = "master" ]
then
    echo "You cannot merge master into master. Exiting for now, please checkout a branch, do some work,"
    echo "commit the work to the branch, then run this script again after you have tested and you are "
    echo "confident it is ready to merge back into master. Thank You."
    exit
else
    echo 'Run update of git branch: '$branch
echo '======================================================'

while true; do
    read -p "Do you wish to merge Feature Branch $branch into master? " yn
    case $yn in
        [Yy]* ) mergeFeatureBranch $branch; break;;
        [Nn]* ) exit;;
        * ) echo "Please answer Y(y) or N(n).";;
    esac
done
fi
exit